<?php

namespace Modules\PaymentGateway\App\Services;

use App\Models\Order;
use App\Models\User;
use App\Services\BaseService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\PaymentGateway\App\Services\Payments\BankPaymentService;
use Modules\PaymentGateway\App\Services\Payments\CodPaymentService;
use Modules\PaymentGateway\App\Services\Payments\PaymentService as Gateway;
use Modules\PaymentGateway\App\Services\Payments\PaypalPaymentService;
use Modules\PaymentGateway\App\Services\Payments\PaystackPaymentService;
use Modules\PaymentGateway\App\Services\Payments\RazorpayPaymentService;
use Modules\PaymentGateway\App\Services\Payments\StripePaymentService;
use Modules\PaymentGateway\App\Services\Payments\WalletPaymentService;
use Symfony\Component\HttpFoundation\Response;

class PaymentManager extends BaseService
{
    protected OrderService $orders;

    /** @var array<string,Gateway> */
    protected array $gateways;

    public function __construct(
        OrderService $orders,
        CodPaymentService $cod,
        BankPaymentService $bank,
        StripePaymentService $stripe,
        WalletPaymentService $wallet,
        PaypalPaymentService $paypal,
        RazorpayPaymentService $razorpay,
        PaystackPaymentService $paystack
    ) {
        $this->orders = $orders;

        $this->gateways = [
            $cod->id() => $cod,
            $bank->id() => $bank,
            $stripe->id() => $stripe,
            $wallet->id() => $wallet,
            $paypal->id() => $paypal,
            $razorpay->id() => $razorpay,
            $paystack->id() => $paystack,
        ];
    }

    public function checkout(string $gatewayKey, User $user, array $customerData): RedirectResponse
    {
        try {
            $order = $this->orders->createFromCart($user, $customerData);
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', __('Order creation failed: ').$e->getMessage());
        }

        if (! $order) {
            return redirect()->route('cart.index')->with('error', __('Cart is empty or order creation failed.'));
        }

        // Clear checkout state from session
        session()->forget('checkout_state');

        return $this->pay($order, $gatewayKey);
    }

    public function pay(Order $order, string $gatewayKey, array $data = []): RedirectResponse
    {
        $gateway = $this->getGateway($gatewayKey);

        if (! $gateway || ! $gateway->isEnabled()) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('Payment gateway unavailable or disabled.'));
        }

        return $gateway->createPayment($order, $data);
    }

    public function success(string $gatewayKey, Order $order, Request $request): RedirectResponse
    {
        $gateway = $this->getGateway($gatewayKey);

        if (! $gateway) {
            $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

            return redirect()
                ->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order)
                ->with('error', __('Invalid payment gateway.'));
        }

        return $gateway->handleSuccess($order, $request);
    }

    public function cancel(string $gatewayKey, Order $order, Request $request): RedirectResponse
    {
        $gateway = $this->getGateway($gatewayKey);

        if ($gateway) {
            return $gateway->handleCancel($order, $request);
        }

        $redirectRoute = $order->type === Order::TYPE_WALLET_DEPOSIT ? 'customer.wallet.index' : 'customer.orders.show';

        return redirect()->route($redirectRoute, $order->type === Order::TYPE_WALLET_DEPOSIT ? [] : $order);
    }

    public function webhook(string $gatewayKey, Request $request): Response
    {
        $gateway = $this->getGateway($gatewayKey);

        if (! $gateway) {
            return response()->json(['error' => 'Invalid gateway'], 404);
        }

        return $gateway->handleWebhook($request);
    }

    protected function getGateway(string $key): ?Gateway
    {
        return $this->gateways[$key] ?? null;
    }

    public function getEnabledGateways(): array
    {
        return array_filter($this->gateways, function ($gateway) {
            return $gateway->isEnabled();
        });
    }

    public function getAvailableDepositGateways(): array
    {
        return array_filter($this->gateways, function ($gateway) {
            return $gateway->isEnabled() && ! in_array($gateway->id(), ['wallet', 'cod']);
        });
    }
}
