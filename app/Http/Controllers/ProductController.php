<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * === Daftar produk untuk public/customer ===
     */
    public function index()
    {
        try {
            $products = Product::with('category')->where('stock', '>', 0)->latest()->get();
            return view('products.index', compact('products'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat data produk: ' . $e->getMessage()]);
        }
    }

    /**
     * === Daftar produk untuk admin ===
     */
    public function adminIndex()
    {
        try {
            $products = Product::with('category')->latest()->get();
            return view('admin.products.index', compact('products'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat data produk: ' . $e->getMessage()]);
        }
    }

    /**
     * === Detail produk untuk public ===
     */
    public function show(Product $product)
    {
        try {
            $product->load('category');
            return view('products.show', compact('product'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat detail produk: ' . $e->getMessage()]);
        }
    }

    /**
     * === Form tambah produk (khusus admin) ===
     */
    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        $categories = Categories::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * === Simpan produk baru (admin only) ===
     */
    public function store(Request $request)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $data = $request->except('image');

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            Product::create($data);

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambah produk: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * === Form edit produk (admin only) ===
     */
    public function edit(Product $product)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        $categories = Categories::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * === Update produk (admin only) ===
     */
    public function update(Request $request, Product $product)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $data = $request->except('image');

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui produk: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * === Hapus produk (admin only) ===
     */
    public function destroy(Product $product)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus produk: ' . $e->getMessage()]);
        }
    }
}