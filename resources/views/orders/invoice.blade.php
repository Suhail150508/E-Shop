<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('common.invoice') }} #{{ $order->order_number }}</title>
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
                    {{ __('common.invoice') }} #{{ $order->order_number }}<br>
                    {{ $order->created_at?->format('Y-m-d H:i') }}
                </div>
            </div>
            <div class="invoice-meta">
                <div>{{ __('common.status') }}: {{ ucfirst($order->status) }}</div>
                <div>{{ __('common.payment') }}: {{ ucfirst($order->payment_status) }}</div>
            </div>
        </div>

        <div class="grid">
            <div class="grid-col">
                <div class="section-title">{{ __('common.billed_to') }}</div>
                <div class="value">
                    {{ $order->customer_name ?? $order->user?->name }}<br>
                    {{ $order->customer_email ?? $order->user?->email }}<br>
                    @if($order->customer_phone)
                        {{ $order->customer_phone }}<br>
                    @endif
                </div>
                @if($order->billing_address)
                    <div class="label" style="margin-top: 6px;">{{ __('common.billing_address') }}</div>
                    <div class="value">
                        {!! nl2br(e($order->billing_address)) !!}
                    </div>
                @endif
            </div>
            <div class="grid-col">
                <div class="section-title">{{ __('common.shipping_address') }}</div>
                <div class="value">
                    @if($order->shipping_address)
                        {!! nl2br(e($order->shipping_address)) !!}
                    @else
                        {{ __('common.same_as_billing') }}
                    @endif
                </div>
            </div>
            <div class="grid-col">
                <div class="section-title">{{ __('common.order_details') }}</div>
                <div class="label">{{ __('common.payment_method') }}</div>
                <div class="value">{{ $order->payment_method ?: __('common.na') }}</div>
                <div class="label" style="margin-top: 6px;">{{ __('common.placed_on') }}</div>
                <div class="value">{{ $order->created_at?->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>{{ __('common.product') }}</th>
                    <th class="text-center">{{ __('common.quantity_label') }}</th>
                    <th class="text-right">{{ __('common.unit_price_label') }}</th>
                    <th class="text-right">{{ __('common.discount') }}</th>
                    <th class="text-right">{{ __('common.line_total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="value">{{ $item->product_name }}</div>
                            @if($item->product_sku)
                                <div class="label">{{ __('common.sku') }}: {{ $item->product_sku }}</div>
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
                <th>{{ __('common.subtotal') }}</th>
                <td class="text-right">{{ $order->currency ?? '' }}{{ number_format($order->sub_total, 2) }}</td>
            </tr>
            @if($order->shipping_cost > 0)
                <tr>
                    <th>{{ __('common.shipping_cost') }}</th>
                    <td class="text-right">{{ $order->currency ?? '' }}{{ number_format($order->shipping_cost, 2) }}</td>
                </tr>
            @endif
            @if($order->tax_amount > 0)
                <tr>
                    <th>{{ __('common.tax') }}</th>
                    <td class="text-right">{{ $order->currency ?? '' }}{{ number_format($order->tax_amount, 2) }}</td>
                </tr>
            @endif
            @if($order->discount_amount > 0)
                <tr>
                    <th>{{ __('common.discount') }}</th>
                    <td class="text-right">-{{ $order->currency ?? '' }}{{ number_format($order->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <th>{{ __('common.grand_total') }}</th>
                <td class="text-right">{{ $order->currency ?? '' }}{{ number_format($order->total, 2) }}</td>
            </tr>
        </table>

        <div class="footer-note">
            {{ __('common.thank_you_for_business') }}
        </div>
    </div>
</body>
</html>
