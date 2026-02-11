<?php

namespace Modules\PaymentGateway\App\Services\Payments;

use App\Models\Order;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CodPaymentService implements PaymentService
{
    protected SettingService $settings;

    public function __construct(SettingService $settings)
    {
        $this->settings = $settings;
    }

    public function id(): string
    {
        return 'cod';
    }

    public function isEnabled(): bool
    {
        return (bool) $this->settings->get('payment_cod_enabled', true);
    }

    public function createPayment(Order $order, array $data = []): RedirectResponse
    {
        $order->update([
            'payment_method' => 'cod',
            'payment_status' => Order::PAYMENT_PENDING,
        ]);

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', __('paymentgateway::payment.cod_order_placed'));
    }

    public function handleSuccess(Order $order, Request $request): RedirectResponse
    {
        return redirect()
            ->route('customer.orders.show', $order);
    }

    public function handleCancel(Order $order, Request $request): RedirectResponse
    {
        return redirect()
            ->route('customer.orders.show', $order);
    }

    public function handleWebhook(Request $request): Response
    {
        return new Response('OK');
    }
}
