<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /** ======================= CUSTOMER ======================= */

    // Daftar pesanan customer
    public function index()
    {
        try {
            if (!Auth::check()) {
                return redirect('/login')->withErrors(['error' => 'Silakan login terlebih dahulu!']);
            }

            $orders = Order::where('user_id', Auth::id())
                ->with('product')
                ->latest()
                ->get();

            return view('customer.orders', compact('orders'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat data: ' . $e->getMessage()]);
        }
    }

    // Form buat pesanan
    public function create(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->withErrors(['error' => 'Silakan login terlebih dahulu!']);
        }

        $products = Product::where('stock', '>', 0)->get();
        $selectedProduct = $request->has('product_id')
            ? Product::find($request->product_id)
            : null;

        return view('customer.orders.create', compact('products', 'selectedProduct'));
    }

    // Simpan pesanan baru
    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect('/login')->withErrors(['error' => 'Silakan login terlebih dahulu!']);
            }

            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity'   => 'required|integer|min:1',
                'address'    => 'required|string',
            ]);

            DB::transaction(function () use ($validated) {
                $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

                if ($product->stock < $validated['quantity']) {
                    throw new \RuntimeException('Stok tidak mencukupi!');
                }

                $total = (int) $product->price * (int) $validated['quantity'];

                Order::create([
                    'user_id'     => Auth::id(),
                    'product_id'  => $product->id,
                    'quantity'    => $validated['quantity'],
                    'total_price' => $total,
                    'status'      => 'pending',
                    'address'     => $validated['address'],
                ]);

                // kurangi stok
                $product->decrement('stock', $validated['quantity']);
            });

            return redirect()->route('orders.index')
                ->with('success', 'Pesanan berhasil dibuat!');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat pesanan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Detail pesanan (customer & admin)
    public function show(Order $order)
    {
        try {
            if (!Auth::check()) {
                return redirect('/login')->withErrors(['error' => 'Silakan login terlebih dahulu!']);
            }

            // hanya admin atau pemilik
            if (Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
                abort(403, 'Aksi tidak diizinkan.');
            }

            $order->load(['user', 'product']);

            if (Auth::user()->role === 'admin') {
                return view('admin.orders.show', compact('order'));
            }

            // pastikan view customer konsisten
            return view('customer.orders.show', compact('order'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat detail: ' . $e->getMessage()]);
        }
    }

    // Batalkan pesanan (customer)
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

            DB::transaction(function () use ($order) {
                // update status
                $order->update(['status' => 'cancelled']);

                // kembalikan stok
                $order->product()->lockForUpdate()->first()->increment('stock', $order->quantity);
            });

            return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibatalkan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membatalkan pesanan: ' . $e->getMessage()]);
        }
    }

    /** ======================= ADMIN ======================= */

    // Daftar pesanan admin + filter ?status=
    public function adminIndex(Request $request)
    {
        try {
            $status = $request->query('status');
            $valid = ['pending', 'processing', 'completed', 'cancelled'];

            $query = Order::with(['user', 'product'])->latest();

            if ($status && in_array($status, $valid, true)) {
                $query->where('status', $status);
            }

            $orders = $query->get();

            // ringkasan untuk kartu
            $stats = [
                'pending'    => Order::where('status', 'pending')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'completed'  => Order::where('status', 'completed')->count(),
                'cancelled'  => Order::where('status', 'cancelled')->count(),
            ];

            return view('admin.orders.index', [
                'orders'        => $orders,
                'stats'         => $stats,
                'currentStatus' => $status,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat data: ' . $e->getMessage()]);
        }
    }

    // Edit pesanan (admin)
    public function edit(Order $order)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        $order->load(['user','product']);
        return view('admin.orders.edit', compact('order'));
    }

    // Update status pesanan (admin)
    public function update(Request $request, Order $order)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            $data = $request->validate([
                'status' => 'required|in:pending,processing,completed,cancelled',
            ]);

            DB::transaction(function () use ($order, $data) {
                $old = $order->status;
                $new = $data['status'];

                // Jika dari non-cancelled -> cancelled, kembalikan stok
                if ($old !== 'cancelled' && $new === 'cancelled') {
                    $order->product()->lockForUpdate()->first()->increment('stock', $order->quantity);
                }

                // Jika dari cancelled -> status lain, kurangi stok kembali (jika stok cukup)
                if ($old === 'cancelled' && $new !== 'cancelled') {
                    $product = $order->product()->lockForUpdate()->first();
                    if ($product->stock < $order->quantity) {
                        throw new \RuntimeException('Stok tidak mencukupi untuk mengaktifkan kembali pesanan ini.');
                    }
                    $product->decrement('stock', $order->quantity);
                }

                $order->update(['status' => $new]);
            });

            return redirect()->route('admin.orders.index')
                ->with('success', 'Status pesanan berhasil diupdate!');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update pesanan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Hapus pesanan (admin)
    public function destroy(Order $order)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            DB::transaction(function () use ($order) {
                // Jika masih belum cancelled, kembalikan stok saat dihapus
                if ($order->status !== 'cancelled') {
                    $order->product()->lockForUpdate()->first()->increment('stock', $order->quantity);
                }
                $order->delete();
            });

            return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus pesanan: ' . $e->getMessage()]);
        }
    }
}