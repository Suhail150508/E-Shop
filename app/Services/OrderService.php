<?php

namespace App\Services;

use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Modules\Product\App\Models\Product;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Modules\PaymentGateway\App\Services\WalletService;

class OrderService extends BaseService
{
    protected CartService $cart;

    protected SettingService $settings;

    protected WalletService $wallet;

    public function __construct(CartService $cart, SettingService $settings, WalletService $wallet)
    {
        $this->cart = $cart;
        $this->settings = $settings;
        $this->wallet = $wallet;
    }

    public function createFromCart(?User $user = null, array $customerData = [])
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            return null;
        }

        if (! $user && Auth::check()) {
            $user = Auth::user();
        }

        return DB::transaction(function () use ($items, $user, $customerData) {
            $subtotal = $this->cart->subtotal();

            $shippingRate = (float) $this->settings->get('shipping_flat_rate', 0);
            $taxPercent = (float) $this->settings->get('tax_percent', 0);

            $shipping = $shippingRate > 0 ? $shippingRate : 0;
            $tax = $taxPercent > 0 ? round($subtotal * $taxPercent / 100, 2) : 0;

            $discountTotal = $this->cart->discount();

            $total = max(0, $subtotal + $shipping + $tax - $discountTotal);

            $orderNumber = $this->generateOrderNumber();

            $baseCurrency = default_currency();
            $currentCurrency = current_currency();
            $baseCode = $baseCurrency?->code ?? 'USD';
            $orderCurrencyCode = $currentCurrency?->code ?? $baseCode;
            $exchangeRate = $currentCurrency?->rate ?? 1;
            $currency = $currentCurrency?->symbol ?? $this->settings->get('app_currency', '$');

            $order = Order::create([
                'user_id' => $user?->id,
                'order_number' => $orderNumber,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'shipping_total' => $shipping,
                'tax_total' => $tax,
                'total' => $total,
                'currency' => $currency,
                'base_currency' => $baseCode,
                'order_currency' => $orderCurrencyCode,
                'exchange_rate' => $exchangeRate,
                'customer_email' => $customerData['email'] ?? ($user?->email),
                'customer_name' => $customerData['name'] ?? ($user?->name),
                'customer_phone' => $customerData['phone'] ?? null,
                'billing_address' => $customerData['billing_address'] ?? null,
                'shipping_address' => $customerData['shipping_address'] ?? null,
                'shipping_latitude' => $customerData['shipping_latitude'] ?? null,
                'shipping_longitude' => $customerData['shipping_longitude'] ?? null,
                'notes' => $customerData['notes'] ?? null,
                'type' => Order::TYPE_PRODUCT_ORDER,
            ]);

            // Increment coupon usage
            $couponData = $this->cart->getCoupon();
            if ($couponData) {
                $coupon = \App\Models\Coupon::find($couponData['id']);
                if ($coupon) {
                    $coupon->increment('used_count');
                }
            }

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);

                if (! $product) {
                    continue;
                }

                $unit = $item['discount_price'] !== null && $item['discount_price'] > 0 && $item['discount_price'] < $item['price']
                    ? $item['discount_price']
                    : $item['price'];

                $lineTotal = $unit * $item['quantity'];
                $discountAmount = max(0, ($item['price'] - $unit) * $item['quantity']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unit,
                    'discount' => $discountAmount,
                    'total' => $lineTotal,
                ]);
            }

            $this->reduceStock($order);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'changed_by_id' => $user?->id,
            ]);

            $this->cart->clear();

            // Broadcast Event & Notification
            try {
                event(new OrderCreated($order));

                // Notify all admins
                $admins = User::where('role', User::ROLE_ADMIN)->get();
                Notification::send($admins, new NewOrderNotification($order));

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Order created notification failed: '.$e->getMessage());
            }

            return $order->fresh(['items', 'user']);
        });
    }

    public function createDepositOrder(User $user, float $amount, string $paymentMethod): Order
    {
        return DB::transaction(function () use ($user, $amount, $paymentMethod) {
            $orderNumber = $this->generateOrderNumber();
            $baseCurrency = default_currency();
            $currentCurrency = current_currency();
            $baseCode = $baseCurrency?->code ?? 'USD';
            $orderCurrencyCode = $currentCurrency?->code ?? $baseCode;
            $exchangeRate = $currentCurrency?->rate ?? 1;
            $currency = $currentCurrency?->symbol ?? $this->settings->get('app_currency', '$');

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'payment_method' => $paymentMethod,
                'subtotal' => $amount,
                'discount_total' => 0,
                'shipping_total' => 0,
                'tax_total' => 0,
                'total' => $amount,
                'currency' => $currency,
                'base_currency' => $baseCode,
                'order_currency' => $orderCurrencyCode,
                'exchange_rate' => $exchangeRate,
                'customer_email' => $user->email,
                'customer_name' => $user->name,
                'type' => Order::TYPE_WALLET_DEPOSIT,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'changed_by_id' => $user->id,
            ]);

            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => null, // No product for deposit
                'product_name' => 'Wallet Deposit',
                'quantity' => 1,
                'unit_price' => $amount,
                'total' => $amount,
            ]);

            // Create Pending Wallet Transaction
            \Modules\PaymentGateway\App\Models\WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'description' => 'Wallet Deposit via '.ucfirst($paymentMethod),
                'payment_method' => $paymentMethod,
                'payment_transaction_id' => $orderNumber,
                'status' => 'pending',
            ]);

            return $order;
        });
    }

    public function changeStatus(Order $order, string $status, $staffId = false): Order
    {
        $status = strtolower($status);

        $allowedStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
        ];

        if (! in_array($status, $allowedStatuses, true)) {
            return $order;
        }

        return DB::transaction(function () use ($order, $status, $staffId) {
            $previousStatus = $order->status;

            // Check if status matches and staff_id matches (if provided)
            if ($previousStatus === $status && ($staffId === false || $staffId == $order->staff_id)) {
                return $order;
            }

            if ($previousStatus !== Order::STATUS_CANCELLED && $status === Order::STATUS_CANCELLED) {
                $this->restoreStock($order);
            } elseif ($previousStatus === Order::STATUS_CANCELLED && $status !== Order::STATUS_CANCELLED) {
                $this->reduceStock($order);
            }

            // Wallet Deposit Logic
            if ($order->type === Order::TYPE_WALLET_DEPOSIT) {
                if ($status === Order::STATUS_PROCESSING && $previousStatus === Order::STATUS_PENDING) {
                    // Increment User Balance directly to avoid duplicate transaction
                    $order->user->increment('wallet_balance', $order->total);

                    // Update Pending Transaction to Approved
                    \Modules\PaymentGateway\App\Models\WalletTransaction::where('payment_transaction_id', $order->order_number)
                        ->where('status', 'pending')
                        ->update(['status' => 'approved']);

                    $status = Order::STATUS_DELIVERED; // Auto deliver deposits
                }
            }

            $order->status = $status;

            if ($staffId !== false) {
                $order->staff_id = $staffId;
            }

            $order->save();

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $status,
                'changed_by_id' => Auth::id(),
            ]);

            // Broadcast Event & Notification
            try {
                event(new OrderStatusUpdated($order));
                if ($order->user) {
                    $order->user->notify(new OrderStatusNotification($order, $status));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Order status notification failed: '.$e->getMessage());
            }

            return $order->fresh(['items', 'user', 'staff']);
        });
    }

    public function reduceStock(Order $order): void
    {
        if ($order->type === Order::TYPE_WALLET_DEPOSIT) {
            return;
        }

        $order->loadMissing('items.product');

        foreach ($order->items as $item) {
            if (! $item->product) {
                continue;
            }

            $product = $item->product->refresh();

            $newStock = max(0, $product->stock - $item->quantity);

            $product->update(['stock' => $newStock]);
        }
    }

    public function restoreStock(Order $order): void
    {
        if ($order->type === Order::TYPE_WALLET_DEPOSIT) {
            return;
        }

        $order->loadMissing('items.product');

        foreach ($order->items as $item) {
            if (! $item->product) {
                continue;
            }

            $product = $item->product->refresh();

            $product->update([
                'stock' => $product->stock + $item->quantity,
            ]);
        }
    }

    protected function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
