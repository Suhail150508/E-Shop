<?php

namespace Modules\PaymentGateway\App\Services\Payments;

use App\Models\Order;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PaymentGateway\App\Models\WalletTransaction;
use Symfony\Component\HttpFoundation\Response;

class WalletPaymentService implements PaymentService
{
    protected SettingService $settings;

    public function __construct(SettingService $settings)
    {
        $this->settings = $settings;
    }

    public function id(): string
    {
        return 'wallet';
    }

    public function isEnabled(): bool
    {
        return (bool) $this->settings->get('payment_wallet_enabled', true);
    }

    public function createPayment(Order $order, array $data = []): RedirectResponse
    {
        $user = $order->user;

        if (! $user) {
            return back()->with('error', __('paymentgateway::payment.login_required_wallet'));
        }

        if ($user->wallet_balance < $order->total) {
            return back()->with('error', __('paymentgateway::payment.insufficient_wallet_balance'));
        }

        try {
            DB::transaction(function () use ($order, $user) {
                // Lock user row to prevent race conditions (double spending)
                $user = $user->newQuery()->lockForUpdate()->find($user->id);

                if ($user->wallet_balance < $order->total) {
                    throw new \Exception(__('paymentgateway::payment.insufficient_wallet_balance'));
                }

                // Deduct from wallet
                $user->decrement('wallet_balance', $order->total);

                // Create transaction record
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $order->total,
                    'type' => 'debit',
                    'description' => 'Payment for Order #'.$order->order_number,
                    'status' => 'approved',
                ]);

                // Update order status
                $order->update([
                    'payment_method' => 'wallet',
                    'payment_status' => Order::PAYMENT_PAID,
                ]);
            });

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('success', __('paymentgateway::payment.order_paid_wallet'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Wallet payment failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            return back()->with('error', __('paymentgateway::payment.payment_failed'));
        }
    }

    public function handleSuccess(Order $order, Request $request): RedirectResponse
    {
        return redirect()->route('customer.orders.show', $order);
    }

    public function handleCancel(Order $order, Request $request): RedirectResponse
    {
        return redirect()->route('customer.orders.show', $order);
    }

    public function handleWebhook(Request $request): Response
    {
        return new Response('OK');
    }
}
