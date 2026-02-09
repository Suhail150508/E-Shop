<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Modules\Product\App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Total Revenue (Paid product orders only)
        $totalRevenue = Order::where('type', '!=', Order::TYPE_WALLET_DEPOSIT)
            ->where('payment_status', Order::PAYMENT_PAID)
            ->sum('total');

        // 2. Total Orders (exclude wallet deposits for consistency with orders index)
        $totalOrders = Order::where('type', '!=', Order::TYPE_WALLET_DEPOSIT)->count();

        // 3. Total Customers
        $totalCustomers = User::where('role', User::ROLE_CUSTOMER)->count();

        // 4. Total Products
        $totalProducts = Product::count();

        // 5. Recent Orders (product orders only)
        $recentOrders = Order::with('user')
            ->where('type', '!=', Order::TYPE_WALLET_DEPOSIT)
            ->latest()
            ->take(5)
            ->get();

        // 6. Revenue Chart Data (Last 12 months, product orders only)
        $query = Order::where('type', '!=', Order::TYPE_WALLET_DEPOSIT)
            ->where('payment_status', Order::PAYMENT_PAID)
            ->whereYear('created_at', date('Y'));

        if (DB::connection()->getDriverName() === 'sqlite') {
            $revenueData = $query->select(
                DB::raw('sum(total) as sums'),
                DB::raw("strftime('%m', created_at) as month_num")
            )
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get()
            ->mapWithKeys(function ($item) {
                $monthName = date('F', mktime(0, 0, 0, (int)$item->month_num, 10));
                return [$monthName => $item->sums];
            })
            ->toArray();
        } else {
            $revenueData = $query->select(
                DB::raw('sum(total) as sums'),
                DB::raw("MONTH(created_at) as month_num"),
                DB::raw("DATE_FORMAT(created_at,'%M') as month_name")
            )
            ->groupBy('month_num', 'month_name')
            ->orderBy('month_num')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->month_name => $item->sums];
            })
            ->toArray();
        }

        // Fill missing months
        $allMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $chartRevenue = [];
        foreach ($allMonths as $month) {
            $chartRevenue[] = (float) ($revenueData[$month] ?? 0);
        }

        // 7. Order Status Chart Data (product orders only)
        $orderStatusCounts = Order::where('type', '!=', Order::TYPE_WALLET_DEPOSIT)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($item) => (int) $item)
            ->toArray();

        $chartStatusLabels = array_map('ucfirst', array_keys($orderStatusCounts));
        $chartStatusSeries = array_values($orderStatusCounts);

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            'recentOrders',
            'chartRevenue',
            'chartStatusLabels',
            'chartStatusSeries',
            'allMonths'
        ));
    }
}
