<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalOrders = Order::where('staff_id', $user->id)->count();
        $pendingOrders = Order::where('staff_id', $user->id)->where('status', Order::STATUS_PENDING)->count();
        $processingOrders = Order::where('staff_id', $user->id)->where('status', Order::STATUS_PROCESSING)->count();
        $completedOrders = Order::where('staff_id', $user->id)->where('status', Order::STATUS_DELIVERED)->count();

        $recentOrders = Order::where('staff_id', $user->id)
            ->with(['user'])
            ->latest()
            ->take(5)
            ->get();

        // Chart Data (Completed Orders per Month)
        $orderData = Order::where('staff_id', $user->id)
            ->where('status', Order::STATUS_DELIVERED)
            ->select(
                DB::raw('count(*) as count'),
                DB::raw("DATE_FORMAT(created_at,'%M') as months")
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('months')
            ->orderBy('created_at')
            ->pluck('count', 'months')
            ->toArray();

        // Fill missing months
        $allMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $chartData = [];
        foreach ($allMonths as $month) {
            $chartData[] = $orderData[$month] ?? 0;
        }

        return view('staff.dashboard', compact('totalOrders', 'pendingOrders', 'processingOrders', 'completedOrders', 'recentOrders', 'chartData', 'allMonths'));
    }
}
