<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    // Tampilkan semua kategori (untuk public/customer)
    public function index()
    {
        try {
            $categories = Categories::withCount('products')->latest()->get();
            return view('categories.index', compact('categories'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat data: ' . $e->getMessage()]);
        }
    }

    // Tampilkan kategori untuk admin
    public function adminIndex()
    {
        try {
            $categories = Categories::withCount('products')->latest()->get();
            return view('admin.categories.index', compact('categories'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat data: ' . $e->getMessage()]);
        }
    }

    // Form tambah kategori (hanya admin)
    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.categories.create');
    }

    // Simpan kategori baru
    public function store(Request $request)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
            ]);

            Categories::create($request->all());

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambah kategori: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Detail kategori
    public function show(Categories $category)
    {
        try {
            $category->load('products');
            return view('categories.show', compact('category'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat detail: ' . $e->getMessage()]);
        }
    }

    // Form edit kategori (admin only)
    public function edit(Categories $category)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.categories.edit', compact('category'));
    }

    // Update kategori
    public function update(Request $request, Categories $category)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'description' => 'nullable|string',
            ]);

            $category->update($request->all());

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil diupdate!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update kategori: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Hapus kategori (admin only)
    public function destroy(Categories $category)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            if ($category->products()->count() > 0) {
                return back()->withErrors(['error' => 'Tidak dapat menghapus kategori yang masih memiliki produk!']);
            }

            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal hapus kategori: ' . $e->getMessage()]);
        }
    }
}