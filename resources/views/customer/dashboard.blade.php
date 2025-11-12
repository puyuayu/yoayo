<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .product-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .category-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
        }
        .stat-card {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            border: none;
            color: white;
        }
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
    </style>
</head>
<body>
    @extends('layout')

    @section('content')
    <div class="container-fluid mt-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card welcome-card">
                    <div class="card-body py-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">Selamat Datang, {{ Auth::user()->name }}! 🎉</h2>
                                <p class="mb-0 opacity-75">Temukan berbagai dessert lezat untuk hari Anda</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                                            <small>Total Pesanan</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h3 class="mb-0">{{ $stats['pending_orders'] }}</h3>
                                            <small>Pending</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Featured Products -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-primary">
                        <i class="fas fa-star me-2"></i>Produk Unggulan
                    </h3>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                
                <div class="row g-4">
                    @forelse($featured_products as $product)
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card product-card h-100">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         class="card-img-top" 
                                         alt="{{ $product->name }}"
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <span class="badge bg-info">{{ $product->category->name ?? '-' }}</span>
                                    </div>
                                    
                                    <p class="card-text text-muted small flex-grow-1">
                                        {{ Str::limit($product->description, 80) }}
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="h4 text-primary mb-0">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </span>
                                            <small class="text-muted">
                                                Stok: <strong>{{ $product->stock }}</strong>
                                            </small>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('products.show', $product->id) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                            @if($product->stock > 0)
                                                <a href="{{ route('orders.create') }}?product_id={{ $product->id }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-shopping-cart me-1"></i>Pesan Sekarang
                                                </a>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fas fa-times me-1"></i>Stok Habis
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Belum ada produk</h4>
                                <p class="text-muted">Silakan hubungi admin untuk menambahkan produk.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Products by Category -->
        @foreach($categories_with_products as $category)
            @if($category->products->count() > 0)
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-dark">
                                <i class="fas fa-tag me-2"></i>{{ $category->name }}
                            </h3>
                            <a href="{{ route('categories.show', $category->id) }}" class="btn btn-outline-dark btn-sm">
                                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        
                        <div class="row g-3">
                            @foreach($category->products as $product)
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="card product-card h-100">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $product->name }}"
                                                 style="height: 150px; object-fit: cover;">
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                                 style="height: 150px;">
                                                <i class="fas fa-image fa-2x text-muted"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $product->name }}</h6>
                                            <p class="text-primary fw-bold mb-2">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </p>
                                            <div class="d-grid">
                                                <a href="{{ route('products.show', $product->id) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endsection

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>