<?php

namespace Modules\PaymentGateway\App\Services\Payments;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PaymentGateway\App\Models\WalletTransaction;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook as StripeWebhook;
use Symfony\Component\HttpFoundation\Response;

class StripePaymentService implements PaymentService
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
        return 'stripe';
    }

    public function isEnabled(): bool
    {
        return (bool) $this->settings->get('payment_stripe_enabled', false);
    }

    public function createPayment(Order $order, array $data = []): RedirectResponse
    {
        $secretKey = $this->getSecretKey();

        if (! $secretKey) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('paymentgateway::payment.stripe_not_configured'));
        }

        Stripe::setApiKey($secretKey);

        $lineItems = [];

        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $this->getCurrencyCode(),
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                    'unit_amount' => (int) round($item->unit_price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        if (empty($lineItems)) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('paymentgateway::payment.stripe_empty_order'));
        }

        $client = new StripeClient($secretKey);

        try {
            $session = $client->checkout->sessions->create([
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'success_url' => route('payment.success', [
                    'gateway' => 'stripe',
                    'order' => $order->id,
                ]).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel', [
                    'gateway' => 'stripe',
                    'order' => $order->id,
                ]),
                'metadata' => [
                    'order_id' => (string) $order->id,
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'order_id' => (string) $order->id,
                    ],
                ],
            ]);
        } catch (ApiErrorException $exception) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('paymentgateway::payment.stripe_error', ['message' => $exception->getMessage()]));
        }

        $order->update([
            'payment_method' => 'stripe',
        ]);

        return redirect()->away($session->url);
    }

    public function handleSuccess(Order $order, Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('paymentgateway::payment.missing_stripe_session'));
        }

        $secretKey = $this->getSecretKey();

        if (! $secretKey) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('paymentgateway::payment.stripe_not_configured'));
        }

        $client = new StripeClient($secretKey);

        try {
            $session = $client->checkout->sessions->retrieve($sessionId);
        } catch (ApiErrorException $exception) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('paymentgateway::payment.stripe_error', ['message' => $exception->getMessage()]));
        }

        if ($session->payment_status === 'paid') {
            $order->update([
                'payment_status' => Order::PAYMENT_PAID,
            ]);

            if ($order->status === Order::STATUS_PENDING) {
                $this->orders->changeStatus($order, Order::STATUS_PROCESSING, null);
            }

            if ($order->type === Order::TYPE_WALLET_DEPOSIT) {
                return redirect()
                    ->route('customer.wallet.index')
                    ->with('success', __('paymentgateway::payment.wallet_funded'));
            }

            return redirect()
                ->route('checkout.confirmation', $order)
                ->with('success', __('paymentgateway::payment.payment_completed'));
        }

        if ($order->type === Order::TYPE_WALLET_DEPOSIT) {
            return redirect()
                ->route('customer.wallet.index')
                ->with('error', __('paymentgateway::payment.payment_not_completed'));
        }

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('error', __('paymentgateway::payment.payment_not_completed'));
    }

    public function handleCancel(Order $order, Request $request): RedirectResponse
    {
        $order->update([
            'payment_status' => Order::PAYMENT_FAILED,
        ]);

        $this->orders->changeStatus($order, Order::STATUS_CANCELLED, null);

        if ($order->type === Order::TYPE_WALLET_DEPOSIT) {
            // Mark pending transaction as failed
            WalletTransaction::where('payment_transaction_id', $order->order_number)
                ->where('status', 'pending')
                ->update(['status' => 'failed']);

            return redirect()
                ->route('customer.wallet.index')
                ->with('error', __('paymentgateway::payment.payment_cancelled'));
        }

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('error', __('Payment cancelled.'));
    }

    public function handleWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = $this->settings->get('stripe_webhook_secret');

        if ($endpointSecret) {
            try {
                $event = StripeWebhook::constructEvent(
                    $payload,
                    $sigHeader,
                    $endpointSecret
                );
            } catch (\Throwable $exception) {
                return new Response('Invalid payload', 400);
            }
        } else {
            $event = json_decode($payload, true);
        }

        $type = is_array($event) ? ($event['type'] ?? null) : $event->type;

        if ($type === 'checkout.session.completed') {
            $data = is_array($event) ? ($event['data']['object'] ?? []) : $event->data->object;

            $orderId = is_array($data)
                ? ($data['metadata']['order_id'] ?? null)
                : ($data->metadata->order_id ?? null);

            if ($orderId) {
                $order = Order::find($orderId);

                if ($order && $order->payment_status !== Order::PAYMENT_PAID) {
                    $order->update([
                        'payment_status' => Order::PAYMENT_PAID,
                    ]);

                    if ($order->status === Order::STATUS_PENDING) {
                        $this->orders->changeStatus($order, Order::STATUS_PROCESSING, null);
                    }
                }
            }
        } elseif ($type === 'charge.refunded') {
            $data = is_array($event) ? ($event['data']['object'] ?? []) : $event->data->object;

            $orderId = is_array($data)
                ? ($data['metadata']['order_id'] ?? null)
                : ($data->metadata->order_id ?? null);

            $amountRefunded = is_array($data)
                ? ($data['amount_refunded'] ?? 0)
                : ($data->amount_refunded ?? 0);

            // Amount is in cents
            $amount = $amountRefunded / 100;

            if ($orderId && $amount > 0) {
                $order = Order::find($orderId);

                if ($order && $order->user) {
                    // Prevent double refund if already processed
                    if ($order->payment_status === Order::PAYMENT_REFUNDED) {
                        return new Response('Already Refunded', 200);
                    }

                    DB::transaction(function () use ($order, $amount) {
                        $user = $order->user;
                        $user->increment('wallet_balance', $amount);

                        WalletTransaction::create([
                            'user_id' => $user->id,
                            'amount' => $amount,
                            'type' => 'credit',
                            'description' => 'Refund for Order #'.$order->order_number,
                            'payment_method' => 'stripe',
                            'status' => 'approved',
                        ]);

                        if ($amount >= $order->total) {
                            $order->update(['payment_status' => Order::PAYMENT_REFUNDED]);
                        }
                    });
                }
            }
        }

        return new Response('OK', 200);
    }

    protected function getSecretKey(): ?string
    {
        $mode = $this->settings->get('stripe_mode', 'test');

        if ($mode === 'live') {
            $key = $this->settings->get('stripe_live_secret_key');
        } else {
            $key = $this->settings->get('stripe_test_secret_key');
        }

        return $key ?: null;
    }

    protected function getCurrencyCode(): string
    {
        // Try to get code from settings first (if stored as code)
        $currency = $this->settings->get('app_currency_code');
        
        if ($currency) {
            return strtolower($currency);
        }

        // Fallback: Get default currency from database
        $defaultCurrency = \App\Models\Currency::where('is_default', true)->value('code');
        
        return strtolower($defaultCurrency ?? 'usd');
    }
}
