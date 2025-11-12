<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    // Dashboard customer - fokus produk
    public function dashboard()
    {
        try {
            $user = Auth::user();
            
            // Stats sederhana
            $stats = [
                'total_orders' => Order::where('user_id', $user->id)->count(),
                'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            ];

            // Featured products - produk terbaru
            $featured_products = Product::with('category')
                ->where('stock', '>', 0)
                ->latest()
                ->take(8)
                ->get();

            // Products by category
            $categories_with_products = Categories::with(['products' => function($query) {
                $query->where('stock', '>', 0)->take(4);
            }])->get();

            // Recent orders
            $recent_orders = Order::where('user_id', $user->id)
                ->with('product')
                ->latest()
                ->take(3)
                ->get();

            return view('customer.dashboard', compact(
                'stats', 
                'featured_products', 
                'categories_with_products',
                'recent_orders'
            ));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat dashboard: ' . $e->getMessage()]);
        }
    }

    // Profile customer (tetap sama)
    public function profile()
    {
        return view('customer.profile', ['user' => Auth::user()]);
    }

    // Update profile customer (tetap sama)
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string'
            ]);

            $user->update($request->all());
            
            return redirect()->route('customer.profile')->with('success', 'Profile berhasil diupdate!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update profile: ' . $e->getMessage()])
                ->withInput();
        }
    }
}