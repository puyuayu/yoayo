@extends('layouts.customer')

@section('title', 'Pesanan Saya')

@section('content')
<div class="container mt-4">
    <h3 class="fw-bold mb-4 text-center text-primary">
        <i class="bi bi-bag-check"></i> Pesanan Saya
    </h3>

    @if ($orders->isEmpty())
        <div class="text-center my-5">
            <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" width="100" alt="no orders">
            <h5 class="mt-3 text-secondary">Belum ada pesanan</h5>
            <p class="text-muted">Ayo pesan dessert favoritmu sekarang juga 🍰</p>
            <a href="{{ route('customer.products') }}" class="btn btn-primary">
                <i class="bi bi-shop"></i> Lihat Produk
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle shadow-sm">
                <thead class="text-center" style="background: linear-gradient(135deg, #9b7bf9, #6a63ff); color: white;">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $index => $order)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            @foreach ($order->orderItems as $item)
                                <div>{{ $item->product->nama_produk }} ({{ $item->jumlah }})</div>
                            @endforeach
                        </td>
                        <td class="text-center">{{ $order->orderItems->sum('jumlah') }}</td>
                        <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                        <td>
                            @if ($order->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif ($order->status == 'proses')
                                <span class="badge bg-primary">Diproses</span>
                            @elseif ($order->status == 'selesai')
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-danger">Dibatalkan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<footer class="mt-5 py-3 text-center" style="background-color: #f8f9fa;">
    <small class="text-muted">
        © 2025 Dessertique — Semua Hak Dilindungi.
    </small>
</footer>
@endsection
