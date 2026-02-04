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

class RazorpayPaymentService implements PaymentService
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
        return 'razorpay';
    }

    public function isEnabled(): bool
    {
        return (bool) $this->settings->get('payment_razorpay_enabled', false)
            && $this->getKey()
            && $this->getSecret();
    }

    public function createPayment(Order $order, array $data = []): RedirectResponse
    {
        $response = Http::withBasicAuth($this->getKey(), $this->getSecret())
            ->post('https://api.razorpay.com/v1/payment_links', [
                'amount' => (int) round($order->total * 100), // Amount in paise
                'currency' => $this->getCurrencyCode(),
                'accept_partial' => false,
                'reference_id' => (string) $order->id,
                'description' => 'Order #'.$order->order_number,
                'customer' => [
                    'name' => $order->customer_name ?? 'Guest',
                    'email' => $order->customer_email,
                    'contact' => $order->customer_phone ?? '',
                ],
                'notify' => ['sms' => true, 'email' => true],
                'reminder_enable' => true,
                'notes' => [
                    'order_id' => (string) $order->id,
                ],
                'callback_url' => route('payment.success', ['gateway' => 'razorpay', 'order' => $order->id]),
                'callback_method' => 'get',
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $order->update(['payment_method' => 'razorpay']);

            return redirect()->away($data['short_url']);
        }

        $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

        return redirect()
            ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
            ->with('error', __('Razorpay error: ').($response->json()['error']['description'] ?? 'Unknown error'));
    }

    public function handleSuccess(Order $order, Request $request): RedirectResponse
    {
        $paymentId = $request->query('razorpay_payment_id');
        $paymentLinkId = $request->query('razorpay_payment_link_id');
        $signature = $request->query('razorpay_signature');

        $expectedSignature = hash_hmac('sha256', $paymentLinkId.'|'.$paymentId, $this->getSecret());

        if (hash_equals($expectedSignature, $signature)) {
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
                ->route('customer.orders.show', $order)
                ->with('success', __('Payment completed successfully.'));
        }

        $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

        return redirect()
            ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
            ->with('error', __('Payment failed. Invalid signature.'));
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
        $webhookSecret = $this->settings->get('razorpay_webhook_secret');
        $signature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();

        if ($webhookSecret) {
            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            if (! hash_equals($expectedSignature, $signature)) {
                return new Response('Invalid Signature', 400);
            }
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? null;

        if ($event === 'refund.processed') {
            $refund = $data['payload']['refund']['entity'];
            $payment = $data['payload']['payment']['entity'];

            $orderId = $payment['notes']['order_id'] ?? null;

            if ($orderId) {
                $order = Order::find($orderId);
                if ($order && $order->user) {
                    $amount = $refund['amount'] / 100;
                    DB::transaction(function () use ($order, $amount) {
                        $user = $order->user;
                        $user->increment('wallet_balance', $amount);
                        WalletTransaction::create([
                            'user_id' => $user->id,
                            'amount' => $amount,
                            'type' => 'credit',
                            'description' => 'Refund for Order #'.$order->order_number,
                            'payment_method' => 'razorpay',
                            'status' => 'approved',
                        ]);
                        if ($amount >= $order->total) {
                            $order->update(['payment_status' => Order::PAYMENT_REFUNDED]);
                        }
                    });
                }
            }
        }

        return new Response('OK');
    }

    protected function getKey(): ?string
    {
        return $this->settings->get('razorpay_key');
    }

    protected function getSecret(): ?string
    {
        return $this->settings->get('razorpay_secret');
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
        
        return strtoupper($defaultCurrency ?? 'INR');
    }
}
