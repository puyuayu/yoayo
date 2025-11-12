<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman register.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi pengguna baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', // default role
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login pengguna.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba login menggunakan Auth bawaan Laravel
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Arahkan sesuai role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Admin!');
            } else {
                return redirect()->route('dashboard')->with('success', 'Login berhasil!');
            }
        }

        // Jika gagal login
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    // ===== ADMIN USER MANAGEMENT METHODS =====

    /**
     * Tampilkan semua user (admin only)
     */
    public function adminUsersIndex()
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            $users = User::where('id', '!=', Auth::id())->latest()->get();
            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat data user: ' . $e->getMessage()]);
        }
    }

    /**
     * Form tambah user (admin only)
     */
    public function adminUsersCreate()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.users.create');
    }

    /**
     * Simpan user baru (admin only)
     */
    public function adminUsersStore(Request $request)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'role' => 'required|in:admin,customer',
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string'
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone' => $request->phone,
                'address' => $request->address
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambah user: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Form edit user (admin only)
     */
    public function adminUsersEdit(User $user)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user (admin only)
     */
    public function adminUsersUpdate(Request $request, User $user)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6|confirmed',
                'role' => 'required|in:admin,customer',
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string'
            ]);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'phone' => $request->phone,
                'address' => $request->address
            ];

            // Update password hanya jika diisi
            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diupdate!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update user: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Hapus user (admin only)
     */
    public function adminUsersDestroy(User $user)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403);
            }

            // Cek jika user mencoba menghapus diri sendiri
            if ($user->id == Auth::id()) {
                return back()->withErrors(['error' => 'Tidak dapat menghapus akun sendiri!']);
            }

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus user: ' . $e->getMessage()]);
        }
    }
}