<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Tampilkan daftar pesanan untuk customer
    public function index()
    {
        try {
            if (!Auth::check()) {
                return redirect('/login')->withErrors(['error' => 'Silakan login terlebih dahulu!']);
            }

            $orders = Order::where('user_id', Auth::id())->with('product')->latest()->get();
            return view('orders.index', compact('orders'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat data: ' . $e->getMessage()]);
        }
    }

    // Tampilkan daftar pesanan untuk admin
    public function adminIndex()
    {
        try {
            $orders = Order::with(['user', 'product'])->latest()->get();
            return view('admin.orders.index', compact('orders'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat data: ' . $e->getMessage()]);
        }
    }

    // Form tambah pesanan
    public function create()
    {
        if (!Auth::check()) {
            return redirect('/login')->withErrors(['error' => 'Silakan login terlebih dahulu!']);
        }

        $products = Product::where('stock', '>', 0)->get();
        return view('orders.create', compact('products'));
    }

    // Simpan pesanan baru
    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect('/login')->withErrors(['error' => 'Silakan login terlebih dahulu!']);
            }

            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'address' => 'required|string',
            ]);

            $product = Product::findOrFail($request->product_id);

            // Cek stok
            if ($product->stock < $request->quantity) {
                return back()->withErrors(['error' => 'Stok tidak mencukupi!'])->withInput();
            }

            $total_price = $product->price * $request->quantity;

            // Buat pesanan
            Order::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'total_price' => $total_price,
                'status' => 'pending',
                'address' => $request->address,
            ]);

            // Kurangi stok produk
            $product->decrement('stock', $request->quantity);

            return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat pesanan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Detail pesanan
    public function show(Order $order)
    {
        try {
            if (!Auth::check()) {
                return redirect('/login')->withErrors(['error' => 'Silakan login terlebih dahulu!']);
            }

            // Hanya admin atau pemilik pesanan yang boleh lihat
            if (Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
                abort(403, 'Aksi tidak diizinkan.');
            }

            $order->load(['user', 'product']);
            
            // Tampilkan view yang berbeda untuk admin dan customer
            if (Auth::user()->role === 'admin') {
                return view('admin.orders.show', compact('order'));
            }
            
            return view('orders.show', compact('order'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat detail: ' . $e->getMessage()]);
        }
    }

    // Edit pesanan (admin only)
    public function edit(Order $order)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.orders.edit', compact('order'));
    }

    // Update status pesanan (admin only)
    public function update(Request $request, Order $order)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            $request->validate([
                'status' => 'required|in:pending,processing,completed,cancelled',
            ]);

            $order->update(['status' => $request->status]);

            return redirect()->route('admin.orders.index')->with('success', 'Status pesanan berhasil diupdate!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update pesanan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Hapus pesanan (admin only)
    public function destroy(Order $order)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            $order->delete();

            return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus pesanan: ' . $e->getMessage()]);
        }
    }

    // Batalkan pesanan (user only)
    public function cancel(Order $order)
    {
        try {
            if (!Auth::check()) {
                return redirect('/login')->withErrors(['error' => 'Silakan login terlebih dahulu!']);
            }

            if ($order->user_id !== Auth::id()) {
                abort(403, 'Aksi tidak diizinkan.');
            }

            if (!in_array($order->status, ['pending', 'processing'])) {
                return back()->withErrors(['error' => 'Pesanan sudah tidak bisa dibatalkan.']);
            }

            $order->update(['status' => 'cancelled']);

            $order->product->increment('stock', $order->quantity);

            return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibatalkan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membatalkan pesanan: ' . $e->getMessage()]);
        }
    }
}