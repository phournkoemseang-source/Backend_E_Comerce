<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_revenue' => Order::sum('total_amount') ?? 0,
        ];

        $recent_products = Product::latest()->take(5)->get();
        $recent_orders = Order::latest()->take(5)->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recent_products' => $recent_products,
            'recent_orders' => $recent_orders,
        ]);
    }
}
