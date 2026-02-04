<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Invoice') }} #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #0f172a;
        }

        .invoice-wrapper {
            max-width: 800px;
            margin: 0 auto;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 16px;
        }

        .invoice-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .invoice-meta {
            font-size: 12px;
            color: #6b7280;
        }

        .section-title {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .grid {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
        }

        .grid-col {
            flex: 1;
        }

        .label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .value {
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 8px;
        }

        th {
            background-color: #f3f4f6;
            font-size: 12px;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals {
            width: 260px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals th,
        .totals td {
            border: none;
            padding: 4px 0;
        }

        .totals tr.total-row th,
        .totals tr.total-row td {
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
            font-size: 13px;
        }

        .totals tr.total-row td {
            font-weight: 700;
        }

        .footer-note {
            font-size: 11px;
            color: #6b7280;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <div class="invoice-header">
            <div>
                <div class="invoice-title">{{ config('app.name') }}</div>
                <div class="invoice-meta">
                    {{ __('Invoice') }} #{{ $order->order_number }}<br>
                    {{ $order->created_at?->format('Y-m-d H:i') }}
                </div>
            </div>
            <div class="invoice-meta">
                <div>{{ __('Status') }}: {{ ucfirst($order->status) }}</div>
                <div>{{ __('Payment') }}: {{ ucfirst($order->payment_status) }}</div>
            </div>
        </div>

        <div class="grid">
            <div class="grid-col">
                <div class="section-title">{{ __('Billed to') }}</div>
                <div class="value">
                    {{ $order->customer_name ?? $order->user?->name }}<br>
                    {{ $order->customer_email ?? $order->user?->email }}<br>
                    @if($order->customer_phone)
                        {{ $order->customer_phone }}<br>
                    @endif
                </div>
                @if($order->billing_address)
                    <div class="label" style="margin-top: 6px;">{{ __('Billing address') }}</div>
                    <div class="value">
                        {!! nl2br(e($order->billing_address)) !!}
                    </div>
                @endif
            </div>
            <div class="grid-col">
                <div class="section-title">{{ __('Shipping address') }}</div>
                <div class="value">
                    @if($order->shipping_address)
                        {!! nl2br(e($order->shipping_address)) !!}
                    @else
                        {{ __('Same as billing address') }}
                    @endif
                </div>
            </div>
            <div class="grid-col">
                <div class="section-title">{{ __('Order details') }}</div>
                <div class="label">{{ __('Payment method') }}</div>
                <div class="value">{{ $order->payment_method ?: __('N/A') }}</div>
                <div class="label" style="margin-top: 6px;">{{ __('Placed on') }}</div>
                <div class="value">{{ $order->created_at?->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th class="text-center">{{ __('Quantity') }}</th>
                    <th class="text-right">{{ __('Unit price') }}</th>
                    <th class="text-right">{{ __('Discount') }}</th>
                    <th class="text-right">{{ __('Line total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="value">{{ $item->product_name }}</div>
                            @if($item->product_sku)
                                <div class="label">{{ __('SKU') }}: {{ $item->product_sku }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">
                            {{ $order->currency ?? '' }}{{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="text-right">
                            {{ $order->currency ?? '' }}{{ number_format($item->discount, 2) }}
                        </td>
                        <td class="text-right">
                            {{ $order->currency ?? '' }}{{ number_format($item->total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <th class="text-right">{{ __('Subtotal') }}</th>
                <td class="text-right">
                    {{ $order->currency ?? '' }}{{ number_format($order->subtotal, 2) }}
                </td>
            </tr>
            <tr>
                <th class="text-right">{{ __('Discounts') }}</th>
                <td class="text-right">
                    {{ $order->currency ?? '' }}{{ number_format($order->discount_total, 2) }}
                </td>
            </tr>
            <tr>
                <th class="text-right">{{ __('Shipping') }}</th>
                <td class="text-right">
                    {{ $order->currency ?? '' }}{{ number_format($order->shipping_total, 2) }}
                </td>
            </tr>
            <tr>
                <th class="text-right">{{ __('Tax') }}</th>
                <td class="text-right">
                    {{ $order->currency ?? '' }}{{ number_format($order->tax_total, 2) }}
                </td>
            </tr>
            <tr class="total-row">
                <th class="text-right">{{ __('Total') }}</th>
                <td class="text-right">
                    {{ $order->currency ?? '' }}{{ number_format($order->total, 2) }}
                </td>
            </tr>
        </table>

        @if($order->notes)
            <div class="section-title">{{ __('Notes') }}</div>
            <div class="value">
                {!! nl2br(e($order->notes)) !!}
            </div>
        @endif

        <div class="footer-note">
            {{ __('Thank you for your purchase.') }}
        </div>
    </div>
</body>
</html>

