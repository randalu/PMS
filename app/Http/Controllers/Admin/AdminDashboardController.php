<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'newOrders' => Order::query()->where('status', 'new')->count(),
            'pendingDelivery' => Order::query()->whereIn('status', ['confirmed', 'processing', 'packed', 'dispatched'])->count(),
            'lowStock' => ProductVariant::query()->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count(),
            'products' => Product::query()->count(),
            'recentOrders' => Order::query()->latest()->limit(8)->get(),
        ]);
    }
}
