<?php

namespace Modules\PaymentGateway\App\Services\Payments;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\PaymentGateway\App\Models\WalletTransaction;
use Symfony\Component\HttpFoundation\Response;

class BankPaymentService implements PaymentService
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
        return 'bank';
    }

    public function isEnabled(): bool
    {
        return (bool) $this->settings->get('payment_bank_enabled', false);
    }

    public function createPayment(Order $order, array $data = []): RedirectResponse
    {
        // For Bank Transfer, we mark the order as Pending Payment
        // The user is expected to manually transfer and maybe upload proof later (if that feature exists)
        // or just wait for admin confirmation.

        $notes = $order->notes;
        if (!empty($data['bank_transaction_id'])) {
            $notes .= "\n\nBank Transaction ID: " . $data['bank_transaction_id'];
        }
        if (!empty($data['bank_name'])) {
            $notes .= "\nBank Name: " . $data['bank_name'];
        }

        $order->update([
            'payment_method' => 'bank',
            'payment_status' => Order::PAYMENT_PENDING,
            'notes' => trim($notes),
        ]);

        if ($order->type === Order::TYPE_WALLET_DEPOSIT) {
            WalletTransaction::where('payment_transaction_id', $order->order_number)
                ->where('status', 'pending')
                ->update(['payment_method' => 'bank']);

            return redirect()
                ->route('customer.wallet.index')
                ->with('success', __('paymentgateway::payment.bank_transfer_instructions'));
        }

        // We can redirect to the order confirmation page or show page with a success message
        // encompassing the bank details which should be visible on the order show page or checkout confirmation.
        // Usually, for bank transfer, we show the success page which displays the bank details.

        return redirect()
            ->route('checkout.confirmation', $order)
            ->with('success', __('paymentgateway::payment.bank_transfer_submitted'));
    }

    public function handleSuccess(Order $order, Request $request): RedirectResponse
    {
        // Bank transfer doesn't have an automatic "success" callback usually.
        // This might be called if we had an external flow, but for manual bank transfer,
        // the createPayment does the job.

        return redirect()
            ->route('customer.orders.show', $order);
    }

    public function handleCancel(Order $order, Request $request): RedirectResponse
    {
        $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

        return redirect()
            ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
            ->with('error', __('paymentgateway::payment.payment_cancelled'));
    }

    public function handleWebhook(Request $request): Response
    {
        return new Response('OK');
    }
}
