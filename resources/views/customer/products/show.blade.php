@extends('layout')

@section('title', $product->name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                @else
                    <img src="{{ asset('images/no-image.png') }}" class="card-img-top" alt="No Image">
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <h2 class="fw-bold">{{ $product->name }}</h2>
            <span class="badge bg-info text-dark">{{ $product->category->name ?? 'Uncategorized' }}</span>
            <p class="mt-3">{{ $product->description }}</p>

            <h4 class="text-primary mt-4">Rp{{ number_format($product->price, 0, ',', '.') }}</h4>
            <p class="text-muted">Stok: {{ $product->stock }}</p>

            
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mt-3">⬅️ Kembali</a>
        </div>
    </div>
</div>
@endsection
