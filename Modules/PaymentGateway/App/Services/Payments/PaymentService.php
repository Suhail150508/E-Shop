<?php

namespace Modules\PaymentGateway\App\Services\Payments;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface PaymentService
{
    public function id(): string;

    public function isEnabled(): bool;

    public function createPayment(Order $order, array $data = []): RedirectResponse;

    public function handleSuccess(Order $order, Request $request): RedirectResponse;

    public function handleCancel(Order $order, Request $request): RedirectResponse;

    public function handleWebhook(Request $request): Response;
}
