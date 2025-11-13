@extends('layout')

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
            <a href="{{ route('products.index') }}" class="btn btn-primary">
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
                            <td>{{ optional($order->created_at)->format('d M Y') }}</td>

                            {{-- Produk + jumlah ringkas di kolom produk --}}
                            <td>{{ $order->product->name ?? '-' }} ({{ $order->quantity ?? 0 }})</td>

                            {{-- Kolom jumlah murni --}}
                            <td class="text-center">{{ $order->quantity ?? '-' }}</td>

                            {{-- Pastikan pakai field yang benar dari DB: total_price --}}
                            <td>Rp {{ number_format((float) ($order->total_price ?? 0), 0, ',', '.') }}</td>

                            {{-- Status mengikuti enum di controller: pending|processing|completed|cancelled --}}
                            <td>
                                @switch($order->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                        @break
                                    @case('processing')
                                        <span class="badge bg-primary">Diproses</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Selesai</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($order->status ?? 'unknown') }}</span>
                                @endswitch
                            </td>

                            <td class="text-center">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                @if (in_array($order->status, ['pending', 'processing']))
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Batalkan pesanan ini?')">
                                            Batal
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection