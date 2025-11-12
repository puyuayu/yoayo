<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    // Dashboard customer
    public function dashboard()
    {
        try {
            $user = Auth::user();

            $stats = [
                'total_orders' => Order::where('user_id', $user->id)->count(),
                'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            ];

            $featured_products = Product::with('category')
                ->where('stock', '>', 0)
                ->latest()
                ->take(8)
                ->get();

            $categories_with_products = Categories::with(['products' => function($query) {
                $query->where('stock', '>', 0)->take(4);
            }])->get();

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

    // Tampilkan profil customer
    public function profile()
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id);
        $orderCount = $orders->count();
        $pendingCount = (clone $orders)->where('status', 'pending')->count();

        $lastOrder = (clone $orders)->latest()->first();
        $lastAddress = $lastOrder->address ?? null;
        $lastOrderDate = $lastOrder ? $lastOrder->created_at->format('d M Y') : null;

        return view('customer.profile', compact(
            'user',
            'orderCount',
            'pendingCount',
            'lastAddress',
            'lastOrderDate'
        ));
    }

    // Update profil customer
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Refresh auth session
            Auth::setUser($user->fresh());

            return redirect()->route('customer.profile')->with('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui profil: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    // 🔹 Update password customer
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}
