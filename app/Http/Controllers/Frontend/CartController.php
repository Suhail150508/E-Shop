<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Modules\Product\App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\PaymentGateway\App\Services\PaymentManager;

class CartController extends Controller
{
    protected CartService $cart;

    protected PaymentManager $payments;

    public function __construct(CartService $cart, PaymentManager $payments)
    {
        $this->cart = $cart;
        $this->payments = $payments;
    }

    public function index(Request $request): View
    {
        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();

        $addresses = collect();

        if ($request->user()) {
            $addresses = Address::where('user_id', $request->user()->id)
                ->orderByDesc('is_default')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $gateways = $this->payments->getEnabledGateways();
        $codEnabled = isset($gateways['cod']);
        $stripeEnabled = isset($gateways['stripe']);
        $walletEnabled = isset($gateways['wallet']);

        $defaultGateway = null;
        if ($walletEnabled && $request->user()?->wallet_balance >= $subtotal) {
            $defaultGateway = 'wallet';
        } elseif ($codEnabled) {
            $defaultGateway = 'cod';
        } elseif ($stripeEnabled) {
            $defaultGateway = 'stripe';
        }

        return view('frontend.cart.index', compact('items', 'subtotal', 'addresses', 'codEnabled', 'stripeEnabled', 'walletEnabled', 'defaultGateway'));
    }

    public function store(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:999'],
            'color' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:100'],
        ]);

        $quantity = (int) ($validated['quantity'] ?? $request->input('quantity', 1));
        $quantity = max(1, min(999, $quantity));
        $options = [];
        if (! empty($validated['color'] ?? $request->input('color'))) {
            $options['color'] = $validated['color'] ?? $request->input('color');
        }
        if (! empty($validated['size'] ?? $request->input('size'))) {
            $options['size'] = $validated['size'] ?? $request->input('size');
        }

        $this->cart->add($product, $quantity, $options);

        $message = __(':name has been added to your cart!', ['name' => $product->name]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => $this->cart->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:999'],
        ]);
        $quantity = max(1, min(999, (int) ($validated['quantity'] ?? $request->input('quantity', 1))));

        $this->cart->update($id, $quantity);

        $message = __('Cart updated.');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => $this->cart->count(),
                'subtotal' => $this->cart->subtotal(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(string $id): JsonResponse|RedirectResponse
    {
        $this->cart->remove($id);

        $message = __('Item removed from cart.');

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => $this->cart->count(),
                'subtotal' => $this->cart->subtotal(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function clear(): JsonResponse|RedirectResponse
    {
        $this->cart->clear();

        $message = __('Cart cleared.');

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => 0,
                'subtotal' => 0,
            ]);
        }

        return back()->with('success', $message);
    }

    public function checkout(Request $request): RedirectResponse
    {
        $gateway = (string) $request->input('payment_method', 'cod');

        $user = $request->user();

        $address = null;

        if ($user) {
            $addressId = $request->input('address_id');

            if ($addressId) {
                $address = Address::where('user_id', $user->id)
                    ->where('id', $addressId)
                    ->first();
            }

            if (! $address) {
                $address = Address::where('user_id', $user->id)
                    ->where('is_default', true)
                    ->first();
            }
        }

        $addressLines = [];

        if ($address) {
            $addressLines[] = $address->line1;

            if ($address->line2) {
                $addressLines[] = $address->line2;
            }

            $cityLineParts = [];

            if ($address->city) {
                $cityLineParts[] = $address->city;
            }

            if ($address->state) {
                $cityLineParts[] = $address->state;
            }

            if ($address->postal_code) {
                $cityLineParts[] = $address->postal_code;
            }

            if (! empty($cityLineParts)) {
                $addressLines[] = implode(', ', $cityLineParts);
            }

            if ($address->country) {
                $addressLines[] = $address->country;
            }
        }

        $addressString = empty($addressLines) ? null : implode("\n", $addressLines);

        $customerData = [
            'name' => $user?->name,
            'email' => $user?->email,
            'phone' => $address?->phone,
            'billing_address' => $addressString,
            'shipping_address' => $addressString,
        ];

        return $this->payments->checkout($gateway, $user, $customerData);
    }
}
