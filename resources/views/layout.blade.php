<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Dessertique') }}</title>

    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        header, footer {
            background: #e9f7ff;
            border-color: #b9e3ff;
        }

        header {
            border-bottom: 2px solid #b9e3ff;
        }

        footer {
            border-top: 2px solid #b9e3ff;
        }

        .nav-link {
            font-weight: 600;
            color: #007bff !important;
        }

        .brand {
            font-weight: 700;
            color: #007bff;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container py-2">
                
                <a class="navbar-brand brand" href="#">Dessertique</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navMenu">

                    <ul class="navbar-nav gap-2">

                        @auth
                            @if(Auth::user()->role === 'admin')
                                <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a></li>
                                <li class="nav-item"><a href="{{ route('admin.users.index') }}" class="nav-link">Pengguna</a></li>
                                <li class="nav-item"><a href="{{ route('admin.products.index') }}" class="nav-link">Produk</a></li>
                                <li class="nav-item"><a href="{{ route('admin.categories.index') }}" class="nav-link">Kategori</a></li>
                                <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link">Pesanan</a></li>
                                <li class="nav-item"><a href="{{ route('logout') }}" class="nav-link">Logout</a></li>

                            @elseif(Auth::user()->role === 'customer')
                                <li class="nav-item"><a href="{{ route('customer.dashboard') }}" class="nav-link">Dashboard</a></li>
                                <li class="nav-item"><a href="{{ route('products.index') }}" class="nav-link">Semua Produk</a></li>
                                <li class="nav-item"><a href="{{ route('categories.index') }}" class="nav-link">Kategori</a></li>
                                <li class="nav-item"><a href="{{ route('orders.index') }}" class="nav-link">Pesanan Saya</a></li>
                                <li class="nav-item"><a href="{{ route('customer.profile') }}" class="nav-link">Profil</a></li>
                                <li class="nav-item"><a href="{{ route('logout') }}" class="nav-link">Logout</a></li>
                            @endif
                        @endauth

                        @guest
                            <li class="nav-item"><a href="{{ url('/') }}" class="nav-link">Home</a></li>
                            <li class="nav-item"><a href="{{ route('products.index') }}" class="nav-link">Produk</a></li>
                            <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">Login</a></li>
                            <li class="nav-item"><a href="{{ route('register.form') }}" class="nav-link">Daftar</a></li>
                        @endguest
                    </ul>

                </div>
            </div>
        </nav>
    </header>

    <main class="py-4">
        @yield('content')
    </main>

    <footer>
        <div class="container text-center py-3">
            <p class="m-0 fw-semibold text-primary">
                © {{ date('Y') }} Dessertique — All Rights Reserved.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
