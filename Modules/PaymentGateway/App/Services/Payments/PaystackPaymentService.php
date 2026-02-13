<?php

namespace Modules\PaymentGateway\App\Services\Payments;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Modules\PaymentGateway\App\Models\WalletTransaction;
use Symfony\Component\HttpFoundation\Response;

class PaystackPaymentService implements PaymentService
{
    protected SettingService $settings;

    protected OrderService $orders;

    public function __construct(SettingService $settings, OrderService $orders)
    {
        $this->settings = $settings;
        $this->orders = $orders;
    }

    public function id(): string
    {
        return 'paystack';
    }

    public function isEnabled(): bool
    {
        return (bool) $this->settings->get('payment_paystack_enabled', false);
    }

    public function createPayment(Order $order, array $data = []): RedirectResponse
    {
        if (! $this->getPublicKey() || ! $this->getSecretKey()) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('paymentgateway::payment.paystack_not_configured'));
        }

        $response = Http::withToken($this->getSecretKey())->post('https://api.paystack.co/transaction/initialize', [
            'email' => $order->customer_email,
            'amount' => (int) round($order->total * 100), // Amount in kobo
            'currency' => $this->getCurrencyCode(),
            'reference' => (string) $order->id.'_'.time(), // Unique reference
            'callback_url' => route('payment.success', ['gateway' => 'paystack', 'order' => $order->id]),
            'metadata' => [
                'order_id' => (string) $order->id,
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if ($data['status']) {
                $order->update(['payment_method' => 'paystack']);

                return redirect()->away($data['data']['authorization_url']);
            }
        }

        $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

        return redirect()
            ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
            ->with('error', __('Paystack error: ').($response->json()['message'] ?? 'Unknown error'));
    }

    public function handleSuccess(Order $order, Request $request): RedirectResponse
    {
        $reference = $request->query('reference');

        if (! $reference) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('Invalid Paystack reference.'));
        }

        $response = Http::withToken($this->getSecretKey())->get("https://api.paystack.co/transaction/verify/{$reference}");

        if ($response->successful()) {
            $data = $response->json();
            if ($data['status'] && $data['data']['status'] === 'success') {
                $order->update(['payment_status' => Order::PAYMENT_PAID]);

                if ($order->status === Order::STATUS_PENDING) {
                    $this->orders->changeStatus($order, Order::STATUS_PROCESSING, null);
                }

                if ($order->type === Order::TYPE_WALLET_DEPOSIT) {
                    return redirect()
                        ->route('customer.wallet.index')
                        ->with('success', __('Wallet funded successfully.'));
                }

                return redirect()
                    ->route('checkout.confirmation', $order)
                    ->with('success', __('Payment completed successfully.'));
            }
        }

        $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

        return redirect()
            ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
            ->with('error', __('Payment failed.'));
    }

    public function handleCancel(Order $order, Request $request): RedirectResponse
    {
        $order->update(['payment_status' => Order::PAYMENT_FAILED]);
        $this->orders->changeStatus($order, Order::STATUS_CANCELLED, null);

        // Mark pending transaction as failed
        WalletTransaction::where('payment_transaction_id', $order->order_number)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);

        return redirect()->route('customer.orders.show', $order)->with('error', __('Payment cancelled.'));
    }

    public function handleWebhook(Request $request): Response
    {
        // Paystack webhook verification
        // X-Paystack-Signature header
        $secret = $this->getSecretKey();
        $signature = $request->header('X-Paystack-Signature');
        $payload = $request->getContent();

        if ($secret) {
            $expectedSignature = hash_hmac('sha512', $payload, $secret);
            if (! hash_equals($expectedSignature, $signature)) {
                return new Response('Invalid Signature', 400);
            }
        }

        $event = json_decode($payload, true);
        $type = $event['event'] ?? null;

        if ($type === 'refund.processed') {
            $data = $event['data'];
            // In Paystack refund event, we have transaction_reference.
            // But we stored order_id in metadata of transaction.
            // Does refund event include original transaction metadata?
            // Usually not directly in the top level, but it might be linked.
            // Alternatively, we can use the transaction reference which we set as "orderId_timestamp".
            // Let's parse reference.

            $transactionReference = $data['transaction_reference'] ?? null;
            if ($transactionReference) {
                $parts = explode('_', $transactionReference);
                $orderId = $parts[0] ?? null;

                if ($orderId) {
                    $order = Order::find($orderId);
                    if ($order && $order->user) {
                        // Prevent double refund if already processed
                        if ($order->payment_status === Order::PAYMENT_REFUNDED) {
                            return new Response('Already Refunded');
                        }

                        $amount = $data['amount'] / 100;
                        DB::transaction(function () use ($order, $amount) {
                            $user = $order->user;
                            $user->increment('wallet_balance', $amount);
                            WalletTransaction::create([
                                'user_id' => $user->id,
                                'amount' => $amount,
                                'type' => 'credit',
                                'description' => 'Refund for Order #'.$order->order_number,
                                'payment_method' => 'paystack',
                                'status' => 'approved',
                            ]);
                            if ($amount >= $order->total) {
                                $order->update(['payment_status' => Order::PAYMENT_REFUNDED]);
                            }
                        });
                    }
                }
            }
        }

        return new Response('OK');
    }

    protected function getPublicKey(): ?string
    {
        return $this->settings->get('paystack_public_key');
    }

    protected function getSecretKey(): ?string
    {
        return $this->settings->get('paystack_secret_key');
    }

    protected function getCurrencyCode(): string
    {
        // Try to get code from settings first
        $currency = $this->settings->get('app_currency_code');
        
        if ($currency) {
            return strtoupper($currency);
        }

        // Fallback: Get default currency from database
        $defaultCurrency = \App\Models\Currency::where('is_default', true)->value('code');
        
        return strtoupper($defaultCurrency ?? 'NGN');
    }
}
