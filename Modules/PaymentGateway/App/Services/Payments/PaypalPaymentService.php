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

class PaypalPaymentService implements PaymentService
{
    protected SettingService $settings;

    protected OrderService $orders;

    protected string $baseUrl;

    public function __construct(SettingService $settings, OrderService $orders)
    {
        $this->settings = $settings;
        $this->orders = $orders;
        $this->baseUrl = $this->settings->get('paypal_mode', 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    public function id(): string
    {
        return 'paypal';
    }

    public function isEnabled(): bool
    {
        return (bool) $this->settings->get('payment_paypal_enabled', false);
    }

    public function createPayment(Order $order, array $data = []): RedirectResponse
    {
        if (! $this->getClientId() || ! $this->getSecret()) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('paymentgateway::payment.paypal_not_configured'));
        }

        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('paymentgateway::payment.paypal_not_configured'));
        }

        $response = Http::withToken($accessToken)->post("{$this->baseUrl}/v2/checkout/orders", [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $order->id,
                'custom_id' => $order->id,
                'amount' => [
                    'currency_code' => $this->getCurrencyCode(),
                    'value' => number_format($order->total, 2, '.', ''),
                ],
            ]],
            'application_context' => [
                'return_url' => route('payment.success', ['gateway' => 'paypal', 'order' => $order->id]),
                'cancel_url' => route('payment.cancel', ['gateway' => 'paypal', 'order' => $order->id]),
                'brand_name' => config('app.name'),
                'user_action' => 'PAY_NOW',
            ],
        ]);

        if ($response->successful()) {
            $links = $response->json()['links'];
            foreach ($links as $link) {
                if ($link['rel'] === 'approve') {
                    $order->update(['payment_method' => 'paypal']);

                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()
            ->route($order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show', $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
            ->with('error', __('PayPal error: ').$response->body());
    }

    public function handleSuccess(Order $order, Request $request): RedirectResponse
    {
        $token = $request->query('token');

        if (! $token) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('Invalid PayPal token.'));
        }

        $accessToken = $this->getAccessToken();
        if (! $accessToken) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('PayPal configuration error.'));
        }

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v2/checkout/orders/{$token}/capture", [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

        if ($response->successful()) {
            $data = $response->json();
            if ($data['status'] === 'COMPLETED') {
                $order->update(['payment_status' => Order::PAYMENT_PAID]);
                if ($order->status === Order::STATUS_PENDING) {
                    $this->orders->changeStatus($order, Order::STATUS_PROCESSING, null);
                }

                return redirect()
                    ->route($order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show', $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                    ->with('success', __('Payment completed successfully.'));
            }
        }

        return redirect()
            ->route('customer.orders.show', $order)
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
        $payload = $request->json()->all();
        $eventType = $payload['event_type'] ?? null;

        if ($eventType === 'PAYMENT.CAPTURE.REFUNDED') {
            $resource = $payload['resource'] ?? [];
            $customId = $resource['custom_id'] ?? null;

            if ($customId) {
                $order = Order::find($customId);
                if ($order && $order->user) {
                    // Prevent double refund if already processed
                    if ($order->payment_status === Order::PAYMENT_REFUNDED) {
                        return new Response('Already Refunded');
                    }

                    $amountRefunded = $resource['amount']['value'] ?? 0;
                    if ($amountRefunded > 0) {
                        DB::transaction(function () use ($order, $amountRefunded) {
                            $user = $order->user;
                            $user->increment('wallet_balance', $amountRefunded);
                            WalletTransaction::create([
                                'user_id' => $user->id,
                                'amount' => $amountRefunded,
                                'type' => 'credit',
                                'description' => 'Refund for Order #'.$order->order_number,
                                'payment_method' => 'paypal',
                                'status' => 'approved',
                            ]);
                            if ($amountRefunded >= $order->total) {
                                $order->update(['payment_status' => Order::PAYMENT_REFUNDED]);
                            }
                        });
                    }
                }
            }
        }

        return new Response('OK');
    }

    protected function getAccessToken(): ?string
    {
        $response = Http::withBasicAuth($this->getClientId(), $this->getSecret())
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        return $response->json()['access_token'] ?? null;
    }

    protected function getClientId(): ?string
    {
        return $this->settings->get('paypal_mode', 'sandbox') === 'live'
            ? $this->settings->get('paypal_live_client_id')
            : $this->settings->get('paypal_sandbox_client_id');
    }

    protected function getSecret(): ?string
    {
        return $this->settings->get('paypal_mode', 'sandbox') === 'live'
            ? $this->settings->get('paypal_live_secret')
            : $this->settings->get('paypal_sandbox_secret');
    }

    protected function getCurrencyCode(): string
    {
        // Try to get code from settings first (if stored as code)
        $currency = $this->settings->get('app_currency_code');
        
        if ($currency) {
            return strtoupper($currency);
        }

        // Fallback: Get default currency from database
        $defaultCurrency = \App\Models\Currency::where('is_default', true)->value('code');
        
        return strtoupper($defaultCurrency ?? 'USD');
    }
}
