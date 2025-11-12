@extends('layout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-primary fw-bold">🍰 Semua Produk</h2>

    @if($products->count() > 0)
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 class="card-img-top" alt="{{ $product->name }}">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="card-img-top" alt="No Image">
                        @endif
                        
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($product->description, 60) }}</p>
                            <p class="fw-bold text-primary">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>

                        <div class="card-footer bg-white border-0 text-center">
                            <a href="#" class="btn btn-sm btn-outline-primary w-100">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <img src="{{ asset('images/empty-box.png') }}" alt="No Product" width="100">
            <h5 class="mt-3 text-secondary">Belum ada produk tersedia</h5>
        </div>
    @endif
</div>
@endsection
