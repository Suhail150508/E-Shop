<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('common.invoice') }} #{{ $order->order_number }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #212529;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            padding: 50px;
        }

        /* Top Border Accent */
        .top-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background-color: #D17A5C; /* Theme Primary */
        }

        /* Helpers */
        .w-100 { width: 100%; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .align-top { vertical-align: top; }
        .mb-10 { margin-bottom: 10px; }
        .mt-10 { margin-top: 10px; }
        
        /* Header */
        .header-table {
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 20px;
        }

        .invoice-label {
            font-size: 36px;
            font-weight: 700;
            color: #D17A5C;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            line-height: 1;
        }

        .invoice-meta {
            font-size: 13px;
            color: #6c757d;
            margin-top: 10px;
        }

        .company-name {
            font-size: 20px;
            font-weight: 700;
            color: #212529;
            text-transform: uppercase;
        }

        /* Info Section */
        .info-table {
            width: 100%;
            margin-bottom: 50px;
        }

        .col-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            color: #D17A5C;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
            display: inline-block;
        }

        .col-content {
            font-size: 13px;
            color: #495057;
        }

        .col-content strong {
            color: #212529;
        }

        /* Product Table */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .product-table th {
            background-color: #D17A5C;
            color: #fff;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 600;
            padding: 14px 15px;
            text-align: left;
        }

        .product-table td {
            padding: 14px 15px;
            border-bottom: 1px solid #dee2e6;
            color: #212529;
            font-size: 13px;
        }

        .product-table tr:nth-child(even) td {
            background-color: #f7f4f0;
        }

        .product-table tr:last-child td {
            border-bottom: 2px solid #D17A5C;
        }

        /* Totals */
        .totals-table {
            width: 350px;
            border-collapse: collapse;
            margin-left: auto; /* Float right equivalent */
        }

        .totals-table td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            font-size: 13px;
            color: #495057;
        }

        .totals-table tr:last-child td {
            border-bottom: none;
        }

        .totals-table .label {
            font-weight: 500;
        }

        .totals-table .value {
            font-weight: 600;
            text-align: right;
            color: #212529;
        }

        .grand-total-box {
            background-color: #D17A5C;
            color: #fff;
            padding: 15px 20px;
            font-weight: 700;
            font-size: 18px;
            text-transform: uppercase;
            margin-top: 10px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
        }
        
        /* Since display:flex might fail in PDF for the box, let's use a table inside too if needed, but simple div block usually works. Let's stick to table for the whole totals section to be safe. */
        
        .grand-total-row td {
            background-color: #D17A5C;
            color: #fff;
            padding: 15px 20px;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
            border: none;
        }

        /* Terms */
        .terms-section {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .terms-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: #212529;
            margin-bottom: 8px;
        }

        .terms-text {
            font-size: 12px;
            color: #6c757d;
        }

        /* Footer */
        .footer-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 15px;
            background-color: #2C2C2C;
        }
    </style>
</head>
<body>
    <div class="top-accent"></div>

    <div class="invoice-container">
        <!-- Header Table -->
        <table class="header-table">
            <tr>
                <td class="align-top" style="width: 60%;">
                    <h1 class="invoice-label">{{ __('common.invoice') }}</h1>
                    <div class="invoice-meta">
                        <div><strong>#</strong> {{ $order->order_number }}</div>
                        <div style="margin-top: 4px;"><strong>{{ __('common.date') }}:</strong> {{ $order->created_at?->format('M d, Y') }}</div>
                        <div style="margin-top: 4px;"><strong>{{ __('common.status') }}:</strong> {{ __('common.' . strtolower($order->payment_status)) }}</div>
                    </div>
                </td>
                <td class="align-top text-right">
                    @if(setting('app_logo'))
                        <img src="{{ getImageOrPlaceholder(setting('app_logo'), '150x50') }}" alt="{{ config('app.name') }}" style="max-height: 70px;">
                    @else
                        <div class="company-name">{{ config('app.name') }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Info Table -->
        <table class="info-table">
            <tr>
                <td class="align-top" style="width: 33%; padding-right: 20px;">
                    <div class="col-title">{{ __('common.billed_to') }}</div>
                    <div class="col-content">
                        <strong>{{ $order->customer_name ?? $order->user?->name }}</strong><br>
                        {{ $order->customer_email ?? $order->user?->email }}<br>
                        @if($order->customer_phone)
                            {{ $order->customer_phone }}<br>
                        @endif
                        @if($order->billing_address)
                            <div class="mt-10">
                                {!! nl2br(e($order->billing_address)) !!}
                            </div>
                        @endif
                    </div>
                </td>
                
                @if($order->shipping_address && $order->shipping_address != $order->billing_address)
                <td class="align-top" style="width: 33%; padding-right: 20px;">
                    <div class="col-title">{{ __('common.shipped_to') }}</div>
                    <div class="col-content">
                        <strong>{{ $order->customer_name ?? $order->user?->name }}</strong><br>
                        @if($order->shipping_address)
                            <div class="mt-10">
                                {!! nl2br(e($order->shipping_address)) !!}
                            </div>
                        @endif
                    </div>
                </td>
                @endif

                <td class="align-top" style="width: 33%;">
                    <div class="col-title">{{ __('common.payment_info') }}</div>
                    <div class="col-content">
                        <p style="margin: 0 0 5px;"><strong>{{ __('common.method') }}:</strong> {{ ucfirst($order->payment_method ?: __('common.na')) }}</p>
                        
                        @if($order->payment_method === 'bank')
                            <div style="margin-top: 10px; background: #f7f4f0; padding: 10px; border-radius: 4px;">
                                <strong style="display:block; margin-bottom:5px;">{{ __('common.bank_transfer_details') }}</strong>
                                <span style="font-size: 12px; display: block; line-height: 1.4;">
                                {{ __('common.bank') }}: {{ setting('payment_bank_name', 'N/A') }}<br>
                                {{ __('common.account') }}: {{ setting('payment_bank_account_number', 'N/A') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Product Table -->
        <table class="product-table">
            <thead>
                <tr>
                    <th style="width: 40%;">{{ __('common.product') }}</th>
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
                            <div style="font-weight: 500;">{{ $item->product_name }}</div>
                            @if($item->product_sku)
                                <div style="font-size: 11px; color: #777;">{{ __('common.sku') }}: {{ $item->product_sku }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">
                            {{ $order->currency ?? '' }}{{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="text-right">
                            {{ $order->currency ?? '' }}{{ number_format($item->discount, 2) }}
                        </td>
                        <td class="text-right" style="font-weight: 600;">
                            {{ $order->currency ?? '' }}{{ number_format($item->total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Table -->
        <table class="totals-table">
            <tr>
                <td class="label">{{ __('common.subtotal') }}:</td>
                <td class="value">{{ $order->currency ?? '' }}{{ number_format($order->sub_total, 2) }}</td>
            </tr>
            @if($order->shipping_cost > 0)
                <tr>
                    <td class="label">{{ __('common.shipping_cost') }}:</td>
                    <td class="value">{{ $order->currency ?? '' }}{{ number_format($order->shipping_cost, 2) }}</td>
                </tr>
            @endif
            @if($order->tax_amount > 0)
                <tr>
                    <td class="label">{{ __('common.tax_amount') }}:</td>
                    <td class="value">{{ $order->currency ?? '' }}{{ number_format($order->tax_amount, 2) }}</td>
                </tr>
            @endif
            @if($order->discount_amount > 0)
                <tr>
                    <td class="label">{{ __('common.discount_amount') }}:</td>
                    <td class="value" style="color: #ef4444;">-{{ $order->currency ?? '' }}{{ number_format($order->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total-row">
                <td style="border-radius: 4px 0 0 4px;">{{ __('common.grand_total') }}:</td>
                <td style="text-align: right; border-radius: 0 4px 4px 0;">{{ $order->currency ?? '' }}{{ number_format($order->total, 2) }}</td>
            </tr>
        </table>

        <!-- Terms -->
        <div class="terms-section">
            <div class="terms-title">{{ __('common.terms_conditions_label') }}:</div>
            <div class="terms-text">
                {{ __('common.payment_due_note') }}
            </div>
        </div>
    </div>

    <div class="footer-bar"></div>
</body>
</html>