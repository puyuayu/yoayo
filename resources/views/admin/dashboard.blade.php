@extends('layout')

@section('content')
<div class="container py-4">

    <h2 class="fw-bold mb-4 text-primary">Dashboard Admin</h2>

    {{-- Statistik --}}
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="fw-semibold text-secondary">Total Produk</h6>
                    <h3 class="fw-bold text-primary">{{ $totalProducts }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="fw-semibold text-secondary">Kategori</h6>
                    <h3 class="fw-bold text-primary">{{ $totalCategories }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="fw-semibold text-secondary">Pesanan</h6>
                    <h3 class="fw-bold text-primary">{{ $totalOrders }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="fw-semibold text-secondary">Pengguna</h6>
                    <h3 class="fw-bold text-primary">{{ $totalUsers }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Produk Terbaru --}}
    <div class="card mt-5 shadow-sm border-0">
        <div class="card-header bg-primary text-white fw-semibold">
            Produk Terbaru
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Tanggal Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestProducts as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->name }}</td>

                            <td>
                                {{ $product->category->name ?? '-' }}
                            </td>

                            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>

                            <td>{{ $product->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">
                                Belum ada produk terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
