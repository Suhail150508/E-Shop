<?php

namespace Modules\PaymentGateway\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Modules\PaymentGateway\App\Services\PaymentManager;

class PaymentController extends Controller
{
    protected PaymentManager $payments;

    public function __construct(PaymentManager $payments)
    {
        $this->payments = $payments;
    }

    public function success(string $gateway, Order $order, Request $request)
    {
        return $this->payments->success($gateway, $order, $request);
    }

    public function cancel(string $gateway, Order $order, Request $request)
    {
        return $this->payments->cancel($gateway, $order, $request);
    }

    public function webhook(string $gateway, Request $request)
    {
        return $this->payments->webhook($gateway, $request);
    }
}
