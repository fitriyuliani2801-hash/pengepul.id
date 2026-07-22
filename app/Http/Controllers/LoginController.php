<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('login');
    }

    // Proses validasi dan login
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Cek kredensial ke database
        if (Auth::attempt($credentials)) {
            if (Auth::user()->status !== 'active') {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun Anda belum aktif atau telah diblokir.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            // 3. Arahkan setelah login sukses
            // GANTI 'dashboard' di bawah ini dengan URL menu utama Anda
            // Contoh jika menu utama Anda adalah surat masuk: 'suratmasuk'
            return redirect()->intended('home')->with('success', 'Selamat datang!');
        }



        // 4. Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Menampilkan halaman registrasi
    public function showRegisterForm()
    {
        return view('register');
    }

    // Proses registrasi akun baru
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'role' => ['required', 'string', 'in:customer,staff,admin,driver'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'role' => $request->role,
            'status' => 'active',
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/home')->with('success', 'Akun Anda (' . ucfirst($user->role) . ') berhasil didaftarkan dan Anda otomatis masuk.');
    }
}
