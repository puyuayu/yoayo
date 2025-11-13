<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /** ===================== PUBLIC / CUSTOMER ===================== */

    // List produk (bisa difilter & search)
    public function index(Request $request)
    {
        try {
            $q = Product::with('category')
                ->where('stock', '>', 0)
                ->latest();

            if ($request->filled('category_id')) {
                $q->where('category_id', $request->category_id);
            }
            if ($request->filled('search')) {
                $term = $request->search;
                $q->where(function ($w) use ($term) {
                    $w->where('name', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%");
                });
            }

            $products = $q->get()->each->append('image_url'); // supaya image_url tersedia
            return view('customer.products.index', compact('products'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat data produk: ' . $e->getMessage()]);
        }
    }

    // Detail produk
    public function show(Product $product)
    {
        try {
            $product->load('category');
            $product->append('image_url');
            return view('customer.products.show', compact('product'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat detail produk: ' . $e->getMessage()]);
        }
    }

    /** ========================== ADMIN =========================== */

    public function adminIndex()
    {
        try {
            $products = Product::with('category')->latest()->get()->each->append('image_url');
            return view('admin.products.index', compact('products'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat data produk: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);
        $categories = Categories::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);

            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'price'       => 'required|numeric|min:0',
                'stock'       => 'required|integer|min:0',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            ]);

            // siapkan data tanpa file
            $data = collect($validated)->except('image')->toArray();

            // simpan file jika ada
            if ($request->hasFile('image')) {
                $file   = $request->file('image');
                $name   = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $ext    = $file->getClientOriginalExtension();
                $fname  = uniqid().'_'.$name.'.'.$ext;

                // tersimpan ke storage/app/public/products/xxx
                $data['image'] = $file->storeAs('products', $fname, 'public');
            }

            Product::create($data);

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambah produk: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Product $product)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);
        $categories = Categories::all();
        $product->append('image_url');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);

            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'price'       => 'required|numeric|min:0',
                'stock'       => 'required|integer|min:0',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            ]);

            $data = collect($validated)->except('image')->toArray();

            if ($request->hasFile('image')) {
                // hapus yang lama kalau ada & file-nya eksis
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                $file   = $request->file('image');
                $name   = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $ext    = $file->getClientOriginalExtension();
                $fname  = uniqid().'_'.$name.'.'.$ext;

                $data['image'] = $file->storeAs('products', $fname, 'public');
            }

            $product->update($data);

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui produk: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Product $product)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);

            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus produk: ' . $e->getMessage()]);
        }
    }
}