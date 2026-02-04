<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $ordersQuery = Order::where('user_id', $user->id);

        $totalOrders = (clone $ordersQuery)->count();
        $pendingOrders = (clone $ordersQuery)->where('status', Order::STATUS_PENDING)->count();
        $processingOrders = (clone $ordersQuery)->where('status', Order::STATUS_PROCESSING)->count();
        $deliveredOrders = (clone $ordersQuery)->where('status', Order::STATUS_DELIVERED)->count();
        $recentOrders = (clone $ordersQuery)->latest()->take(5)->get();

        $wishlistCount = Wishlist::where('user_id', $user->id)->count();
        $walletBalance = 0; // Feature not yet implemented
        $supportTicketCount = SupportTicket::where('user_id', $user->id)->count();

        return view('frontend.account.dashboard', compact(
            'user',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'deliveredOrders',
            'recentOrders',
            'wishlistCount',
            'walletBalance',
            'supportTicketCount'
        ));
    }
}
