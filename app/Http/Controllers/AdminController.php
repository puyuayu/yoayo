<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $totalProducts   = Product::count();
        $totalCategories = Categories::count();
        $totalOrders     = Order::count();
        $totalUsers      = User::count();

        $latestProducts = Product::with('category')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalOrders',
            'totalUsers',
            'latestProducts'
        ));
    }
}
