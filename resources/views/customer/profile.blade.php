@extends('layout')

@section('content')
<style>
    main {
        background-color: #f8fafc;
        min-height: 100vh;
        padding-bottom: 60px;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #0d6efd;
        margin: 25px auto 15px;
        width: 80%;
        text-align: left;
    }

    .profile-wrapper {
        max-width: 850px;
        margin: 0 auto 50px;
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        padding: 40px 50px;
    }

    .profile-header {
        text-align: center;
        color: #0d6efd;
        margin-bottom: 25px;
    }

    .profile-header i {
        font-size: 80px;
        margin-bottom: 10px;
    }

    .form-label {
        font-weight: 600;
        color: #444;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid #ccc;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .btn-primary {
        background-color: #0d6efd;
        color: white;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #084298;
    }

    .stats {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .stat-card {
        flex: 1;
        min-width: 220px;
        background: #f8faff;
        border-radius: 12px;
        text-align: center;
        padding: 20px;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
    }

    .stat-card h6 {
        color: #0d6efd;
        font-weight: 600;
    }

    .stat-card p {
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0;
    }

    hr {
        margin: 40px 0 30px;
        border-top: 2px solid #e6e9ef;
    }
</style>

{{-- 🔹 Judul halaman --}}
<h2 class="page-title">
    <i class="fas fa-user me-2"></i> Profil Pengguna
</h2>

<div class="profile-wrapper">

    <div class="profile-header">
        <i class="fas fa-user-circle"></i>
        <h3 class="fw-bold">{{ $user->name }}</h3>
        <p class="text-muted">{{ $user->email }}</p>
    </div>

    {{-- 🔹 Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 🔹 Form Edit Profil --}}
    <form action="{{ route('customer.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Role</label>
                <input type="text" class="form-control bg-light" value="{{ ucfirst($user->role) }}" readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label">Bergabung Sejak</label>
                <input type="text" class="form-control bg-light" value="{{ $user->created_at->format('d M Y') }}" readonly>
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-2"></i> Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- 🔹 Ganti Password --}}
    <hr>
    <h5 class="text-primary mb-3"><i class="fas fa-lock me-2"></i> Ganti Password</h5>

    <form action="{{ route('customer.profile.update.password') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Password Lama</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Password Baru</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="new_password_confirmation" class="form-control" required>
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-key me-2"></i> Ubah Password
            </button>
        </div>
    </form>

    {{-- 🔹 Statistik --}}
    <div class="stats mt-5">
        <div class="stat-card">
            <h6><i class="fas fa-box me-1"></i> Total Pesanan</h6>
            <p>{{ $orderCount ?? 0 }}</p>
        </div>
        <div class="stat-card">
            <h6><i class="fas fa-hourglass-half me-1"></i> Pending</h6>
            <p>{{ $pendingCount ?? 0 }}</p>
        </div>
        <div class="stat-card">
            <h6><i class="fas fa-calendar me-1"></i> Pesanan Terakhir</h6>
            <p>{{ $lastOrderDate ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
