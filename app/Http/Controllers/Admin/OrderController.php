<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'staff'])
            ->where('type', '!=', Order::TYPE_WALLET_DEPOSIT) // Filter out wallet deposits
            ->latest();

        $status = $request->input('status');
        $paymentStatus = $request->input('payment_status');
        $customer = $request->input('customer');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($request->filled('customer')) {
            $customer = str_replace(['%', '_'], ['\\%', '\\_'], $request->input('customer'));
            $query->where(function ($q) use ($customer) {
                $q->whereHas('user', function ($userQuery) use ($customer) {
                    $userQuery->where('name', 'like', '%'.$customer.'%')
                        ->orWhere('email', 'like', '%'.$customer.'%');
                })->orWhere('customer_name', 'like', '%'.$customer.'%')
                    ->orWhere('customer_email', 'like', '%'.$customer.'%');
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate(10)->withQueryString();

        // Get counts for tabs
        $countQuery = Order::where('type', '!=', Order::TYPE_WALLET_DEPOSIT);
        
        $counts = (clone $countQuery)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusCounts = [
            'all' => array_sum($counts),
            Order::STATUS_PENDING => $counts[Order::STATUS_PENDING] ?? 0,
            Order::STATUS_PROCESSING => $counts[Order::STATUS_PROCESSING] ?? 0,
            Order::STATUS_SHIPPED => $counts[Order::STATUS_SHIPPED] ?? 0,
            Order::STATUS_DELIVERED => $counts[Order::STATUS_DELIVERED] ?? 0,
            Order::STATUS_CANCELLED => $counts[Order::STATUS_CANCELLED] ?? 0,
        ];

        $statuses = [
            Order::STATUS_PENDING => __('Pending'),
            Order::STATUS_PROCESSING => __('Processing'),
            Order::STATUS_SHIPPED => __('Shipped'),
            Order::STATUS_DELIVERED => __('Delivered'),
            Order::STATUS_CANCELLED => __('Cancelled'),
        ];

        $paymentStatuses = [
            Order::PAYMENT_PENDING => __('Pending'),
            Order::PAYMENT_PAID => __('Paid'),
            Order::PAYMENT_FAILED => __('Failed'),
            Order::PAYMENT_REFUNDED => __('Refunded'),
        ];

        return view('admin.orders.index', compact('orders', 'statuses', 'paymentStatuses', 'statusCounts'));
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'user', 'staff', 'statusHistories.changedBy']);

        $statuses = [
            Order::STATUS_PENDING => __('Pending'),
            Order::STATUS_PROCESSING => __('Processing'),
            Order::STATUS_SHIPPED => __('Shipped'),
            Order::STATUS_DELIVERED => __('Delivered'),
            Order::STATUS_CANCELLED => __('Cancelled'),
        ];

        $staffUsers = User::where('role', User::ROLE_STAFF)
            ->orderBy('name')
            ->get();

        return view('admin.orders.show', compact('order', 'statuses', 'staffUsers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'staff_id' => 'nullable|exists:users,id',
        ]);

        $status = $request->input('status');
        $staffId = $request->input('staff_id');

        $this->orderService->changeStatus($order, $status, $staffId);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', __('Order updated successfully.'));
    }

    /**
     * Download the invoice for the order.
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function invoice(Order $order)
    {
        $order->load(['items', 'user']);

        $pdf = Pdf::loadView('orders.invoice', [
            'order' => $order,
        ]);

        $filename = 'invoice-'.$order->order_number.'.pdf';

        return $pdf->download($filename);
    }
}
