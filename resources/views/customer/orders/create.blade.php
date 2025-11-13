@extends('layout')

@section('content')
<div class="container mt-4">
    <h2>Form Pemesanan</h2>
    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="product_id" class="form-label">Pilih Produk</label>
            <select name="product_id" id="product_id" class="form-select" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" 
                        {{ isset($selectedProduct) && $selectedProduct->id == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Jumlah</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Alamat Pengiriman</label>
            <textarea name="address" id="address" rows="3" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">Kirim Pesanan</button>
    </form>
</div>
@endsection
