@extends('layout')

@section('title', 'Edit Produk')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Produk</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            &larr; Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ $product->category_id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ $product->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="price" class="form-control"
                               value="{{ $product->price }}" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stock" class="form-control"
                               value={{ $product->stock }} min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label d-block">Gambar Saat Ini</label>
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}"
                                 alt="Gambar Produk"
                                 class="img-thumbnail mb-2"
                                 style="max-height:90px;">
                        @else
                            <span class="text-muted">Belum ada gambar</span>
                        @endif
                        <input type="file" name="image" class="form-control mt-2">
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        Update Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
