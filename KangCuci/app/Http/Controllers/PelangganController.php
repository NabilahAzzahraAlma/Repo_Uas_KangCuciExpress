<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Cabang;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Exports\PelangganExport;
use App\Imports\PelangganImport;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Pelanggan\PelangganRequest;
use App\Models\Transaksi;
use App\Models\JenisLayanan;

// use Spatie\Permission\Models\Role; 

class PelangganController extends Controller
{
    // --- Manajemen Data Pelanggan (Admin/Karyawan) ---

    public function index()
    {
        $title = "Pelanggan";
        $pelanggan = Pelanggan::orderBy('created_at', 'asc')->get();
        return view('dashboard.pelanggan.index', compact('title', 'pelanggan'));
    }

    public function store(PelangganRequest $request)
    {
        $validated = $request->validated();
        $tambah = Pelanggan::create($validated);
        if ($tambah) {
            return to_route('pelanggan')->with('success', 'Pelanggan Berhasil Ditambahkan');
        } else {
            return to_route('pelanggan')->with('error', 'Pelanggan Gagal Ditambahkan');
        }
    }

    public function show(Request $request)
    {
        $pelanggan = Pelanggan::where('pelanggan.id', $request->id)->first();
        return $pelanggan;
    }

    public function edit(Request $request)
    {
        $pelanggan = Pelanggan::find($request->id);
        return $pelanggan;
    }

    public function update(PelangganRequest $request)
    {
        $validated = $request->validated();
        $perbarui = Pelanggan::where('id', $request->id)->update($validated);
        if ($perbarui) {
            return to_route('pelanggan')->with('success', 'Pelanggan Berhasil Diperbarui');
        } else {
            return to_route('pelanggan')->with('error', 'Pelanggan Gagal Diperbarui');
        }
    }

    public function delete(Request $request)
    {
        $hapus = Pelanggan::where('id', $request->id)->delete();
        if ($hapus) {
            abort(200, 'Pelanggan Berhasil Dihapus');
        } else {
            abort(400, 'Pelanggan Gagal Dihapus');
        }
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new PelangganImport, $request->file('impor'));
            return to_route('pelanggan')->with('success', 'Pelanggan Berhasil Ditambahkan');
        } catch (\Exception $ex) {
            Log::info($ex);
            return to_route('pelanggan')->with('error', 'Pelanggan Gagal Ditambahkan');
        }
    }

    public function export()
    {
        return Excel::download(new PelangganExport, 'Data Pelanggan ' . Carbon::now()->format('d-m-Y') . '.xlsx');
    }

    // --- Otentikasi dan Dashboard Pelanggan ---

    /**
     * Menampilkan formulir registrasi khusus pelanggan.
     * Menggunakan view: resources/views/dashboard/auth/register-pelanggan.blade.php
     */
    public function registerForm()
    {
        $title = "Registrasi Pelanggan";
        return view('dashboard.auth.register-pelanggan', compact('title'));
    }

    /**
     * Memproses data registrasi pelanggan baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        Pelanggan::create([
            'user_id' => $user->id,
            'nama' => $request->nama,
            'email' => $request->email,
        ]);

        // Redirect ke rute login pelanggan yang baru
        return redirect()->route('pelanggan.login')->with('success', 'Akun pelanggan berhasil dibuat. Silakan masuk.');
    }

    /**
     * Menampilkan formulir login khusus pelanggan.
     * Menggunakan view: resources/views/dashboard/auth/login.blade.php
     */
    // public function loginForm()
    // {
    //     $title = "Login Pelanggan";
    //     // PERBAIKAN UTAMA: Menggunakan path view yang benar
    //     return view('dashboard.auth.login', compact('title'));
    // }
    public function showLoginForm()
    {
        return view('pelanggan.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/pelanggan/login');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function formPesan()
    {
        $title = "Pesan Laundry";
        $layanan = JenisLayanan::all();
        return view('pelanggan.pesan', compact('title', 'layanan'));
    }

    public function simpanPesanan(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:jenis_layanan,id',
            'jumlah_pakaian' => 'required|integer|min:1',
        ]);
        Transaksi::create([
            'user_id' => Auth::id(),
            'layanan_id' => $request->layanan_id,
            'jumlah_pakaian' => $request->jumlah_pakaian,
            'status' => 'Menunggu',
            'waktu' => now(),
        ]);

        return redirect()->route('pelanggan.status')->with('success', 'Pesanan berhasil dibuat');
    }

    public function statusCucian()
    {
        $title = "Status Cucian Saya";
        $transaksi = Transaksi::where('user_id', Auth::id())->orderBy('waktu', 'desc')->get();
        return view('pelanggan.status', compact('title', 'transaksi'));
    }

    public function dashboard()
    {
        $title = "Dashboard Pelanggan";
        $transaksi = Transaksi::where('user_id', Auth::id())->latest()->get();

        return view('dashboard.pelanggan.index', compact('title', 'transaksi'));
    }
}
