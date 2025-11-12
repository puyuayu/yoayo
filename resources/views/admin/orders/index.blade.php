<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    @extends('layout')

    @section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Pesanan</h2>
            <div class="btn-group">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter Status -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title">Filter Status</h6>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.orders.index') }}" 
                       class="btn btn-outline-primary {{ !request('status') ? 'active' : '' }}">
                        Semua
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
                       class="btn btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">
                        Pending
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" 
                       class="btn btn-outline-info {{ request('status') == 'processing' ? 'active' : '' }}">
                        Processing
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" 
                       class="btn btn-outline-success {{ request('status') == 'completed' ? 'active' : '' }}">
                        Completed
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" 
                       class="btn btn-outline-danger {{ request('status') == 'cancelled' ? 'active' : '' }}">
                        Cancelled
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th style="width: 200px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <small class="text-muted">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</small>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $order->user->name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $order->user->email ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $order->product->name ?? 'N/A' }}</td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($order->status == 'pending') bg-warning
                                                @elseif($order->status == 'processing') bg-info
                                                @elseif($order->status == 'completed') bg-success
                                                @elseif($order->status == 'cancelled') bg-danger
                                                @else bg-secondary @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <!-- Quick Status Update Buttons -->
                                                @if($order->status != 'completed' && $order->status != 'cancelled')
                                                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="processing">
                                                        <button type="submit" class="btn btn-outline-info btn-sm" 
                                                                title="Proses Pesanan" onclick="return confirm('Ubah status menjadi Processing?')">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="btn btn-outline-success btn-sm" 
                                                                title="Selesaikan Pesanan" onclick="return confirm('Ubah status menjadi Completed?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($order->status != 'cancelled')
                                                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                                title="Batalkan Pesanan" onclick="return confirm('Batalkan pesanan ini?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Edit Button for Full Control -->
                                                <a href="{{ route('admin.orders.edit', $order->id) }}" 
                                                   class="btn btn-outline-primary btn-sm" title="Edit Status">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- Delete Button -->
                                                <form action="{{ route('admin.orders.destroy', $order->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                            onclick="return confirm('Hapus pesanan ini?')" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada pesanan.</p>
                        @if(request('status'))
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">Lihat Semua Pesanan</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics -->
        @if($orders->count() > 0)
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h4>{{ $orders->where('status', 'pending')->count() }}</h4>
                        <p>Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4>{{ $orders->where('status', 'processing')->count() }}</h4>
                        <p>Processing</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4>{{ $orders->where('status', 'completed')->count() }}</h4>
                        <p>Completed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h4>{{ $orders->where('status', 'cancelled')->count() }}</h4>
                        <p>Cancelled</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endsection

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>