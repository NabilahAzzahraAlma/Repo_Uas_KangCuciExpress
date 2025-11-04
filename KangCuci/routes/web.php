<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetailLayananTransaksiController;
use App\Http\Controllers\participantController;
use App\Http\Controllers\HargaJenisLayananController;
use App\Http\Controllers\JenisLayananController;
use App\Http\Controllers\JenisPakaianController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LayananCabangController;
use App\Http\Controllers\LayananPrioritasController;
use App\Http\Controllers\LayananTambahanController;
use App\Http\Controllers\MonitoringparticipantController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ProfileController as ProfileController2;
use App\Http\Controllers\RWController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UMRController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard-pelanggan', [PelangganController::class, 'dashboard'])->name('pelanggan.dashboard');
});

Route::middleware('guest')->group(function () {
    // Halaman login (GET)
    Route::get('/pelanggan/login', [PelangganController::class, 'showLoginForm'])->name('pelanggan.login');

    // Proses login (POST)
    Route::post('/pelanggan/login', [PelangganController::class, 'login']);

    Route::get('/pelanggan/login', [PelangganController::class, 'loginForm'])->name('pelanggan.login');

    Route::get('/pelanggan/register', [PelangganController::class, 'registerForm'])->name('pelanggan.register');
});



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard2', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController2::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController2::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController2::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [LandingPageController::class, 'index'])->name('landing-page');
Route::get('/nota', [LandingPageController::class, 'cekTransaksi'])->name('landing-page.nota');

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginAttempt'])->name('login.attempt');
});

Route::group([
    'middleware' => ['auth'],
], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/laundry/logout', [AuthController::class, 'logout'])->name('logout');

    Route::group([
        'prefix' => 'profile',
    ], function () {
        Route::get('/{user:slug}', [ProfileController::class, 'index'])->name('profile');
        Route::get('/ubah/{user:slug}', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/ubah/{user:slug}', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/ubah-password/{user:slug}', [ProfileController::class, 'editPassword'])->name('profile.edit.password');
        Route::post('/ubah-password/{user:slug}', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    });

    Route::group([
        'prefix' => 'cabang',
        'middleware' => ['role:owner|pic'],
    ], function () {

        Route::get('/', [CabangController::class, 'index'])->name('cabang');
        Route::post('/tambah', [CabangController::class, 'store'])->name('cabang.store');
        Route::get('/lihat', [CabangController::class, 'show'])->name('cabang.show');
        Route::get('/ubah', [CabangController::class, 'edit'])->name('cabang.edit');
        Route::post('/ubah', [CabangController::class, 'update'])->name('cabang.update');
        Route::post('/hapus', [CabangController::class, 'delete'])->name('cabang.delete');
        Route::get('/trash', [CabangController::class, 'trash'])->name('cabang.trash');
        Route::post('/pulihkan', [CabangController::class, 'restore'])->name('cabang.restore');
        Route::post('/hapus-permanen', [CabangController::class, 'destroy'])->name('cabang.destroy');
    });

    Route::group([
        'prefix' => 'umr',
        'middleware' => ['role:owner|pic'],
    ], function () {

        Route::get('/', [UMRController::class, 'index'])->name('umr');
        Route::post('/tambah', [UMRController::class, 'store'])->name('umr.store');
        Route::get('/lihat', [UMRController::class, 'show'])->name('umr.show');
        Route::get('/ubah', [UMRController::class, 'edit'])->name('umr.edit');
        Route::post('/ubah', [UMRController::class, 'update'])->name('umr.update');
        Route::post('/hapus', [UMRController::class, 'delete'])->name('umr.delete');
    });

    Route::group([
        'prefix' => 'user/rw',
        'middleware' => ['role:owner'],
    ], function () {

        Route::get('/', [RWController::class, 'index'])->name('rw');
        Route::get('/tambah', [RWController::class, 'create'])->name('rw.create');
        Route::post('/tambah', [RWController::class, 'store'])->name('rw.store');
        Route::get('/lihat/{user:slug}', [RWController::class, 'view'])->name('rw.view');
        Route::get('/ubah/{user:slug}', [RWController::class, 'edit'])->name('rw.edit');
        Route::post('/ubah/{user:slug}', [RWController::class, 'update'])->name('rw.update');
        Route::get('/ubah-password/{user:slug}', [RWController::class, 'editPassword'])->name('rw.edit.password');
        Route::post('/ubah-password/{user:slug}', [RWController::class, 'updatePassword'])->name('rw.update.password');
        Route::post('/hapus', [RWController::class, 'delete'])->name('rw.delete');
        Route::get('/trash/{user:slug}', [RWController::class, 'trash'])->name('rw.trash');
        Route::post('/pulihkan', [RWController::class, 'restore'])->name('rw.restore');
        Route::post('/hapus-permanen', [RWController::class, 'destroy'])->name('rw.destroy');
        Route::post('/impor', [RWController::class, 'import'])->name('rw.import');
        Route::get('/ekspor', [RWController::class, 'export'])->name('rw.export');
    });

    Route::group([
        'prefix' => 'user',
        'middleware' => ['role:owner|manajer_laundry|pic'],
    ], function () {

        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::get('/cabang/{cabang:slug}', [UserController::class, 'indexCabang'])->name('user.cabang');
        Route::get('/cabang/{cabang:slug}/tambah', [UserController::class, 'createUserCabang'])->name('user.cabang.create');
        Route::get('/tambah', [UserController::class, 'create'])->name('user.create');
        Route::post('/tambah', [UserController::class, 'store'])->name('user.store');
        Route::get('/lihat/{user:slug}', [UserController::class, 'view'])->name('user.view');
        Route::get('/ubah/{user:slug}', [UserController::class, 'edit'])->name('user.edit');
        Route::post('/ubah/{user:slug}', [UserController::class, 'update'])->name('user.update');
        Route::get('/ubah-password/{user:slug}', [UserController::class, 'editPassword'])->name('user.edit.password');
        Route::post('/ubah-password/{user:slug}', [UserController::class, 'updatePassword'])->name('user.update.password');
        Route::post('/hapus', [UserController::class, 'delete'])->name('user.delete');
        Route::get('/trash/{user:slug}', [UserController::class, 'trash'])->name('user.trash');
        Route::post('/pulihkan', [UserController::class, 'restore'])->name('user.restore');
        Route::post('/hapus-permanen', [UserController::class, 'destroy'])->name('user.destroy');
        Route::post('/impor', [UserController::class, 'import'])->name('user.import');
        Route::get('/ekspor', [UserController::class, 'export'])->name('user.export');
    });

    Route::group([
        'prefix' => 'layanan-cabang',
        'middleware' => ['role:owner|pic'],
    ], function () {

        Route::get('/', [LayananCabangController::class, 'index'])->name('layanan-cabang');
        Route::get('/{cabang:slug}', [LayananCabangController::class, 'indexCabang'])->name('layanan-cabang.cabang');
        Route::get('/{cabang:slug}/trash', [LayananCabangController::class, 'indexCabangTrash'])->name('layanan-cabang.trash');
    });

    Route::group([
        'prefix' => 'jenis-layanan',
        'middleware' => ['role:owner|manajer_laundry|pic'],
    ], function () {

        Route::get('/', [JenisLayananController::class, 'index'])->name('jenis-layanan');
        Route::post('/tambah', [JenisLayananController::class, 'store'])->name('jenis-layanan.store');
        Route::get('/lihat', [JenisLayananController::class, 'show'])->name('jenis-layanan.show');
        Route::get('/ubah', [JenisLayananController::class, 'edit'])->name('jenis-layanan.edit');
        Route::post('/ubah', [JenisLayananController::class, 'update'])->name('jenis-layanan.update');
        Route::post('/hapus', [JenisLayananController::class, 'delete'])->name('jenis-layanan.delete');
        Route::get('/trash', [JenisLayananController::class, 'trash'])->name('jenis-layanan.trash');
        Route::post('/pulihkan', [JenisLayananController::class, 'restore'])->name('jenis-layanan.restore');
        Route::post('/hapus-permanen', [JenisLayananController::class, 'destroy'])->name('jenis-layanan.destroy');
        Route::post('/impor', [JenisLayananController::class, 'import'])->name('jenis-layanan.import');
        Route::get('/ekspor', [JenisLayananController::class, 'export'])->name('jenis-layanan.export');
    });

    Route::group([
        'prefix' => 'jenis-pakaian',
        'middleware' => ['role:owner|manajer_laundry|pic'],
    ], function () {

        Route::get('/', [JenisPakaianController::class, 'index'])->name('jenis-pakaian');
        Route::post('/tambah', [JenisPakaianController::class, 'store'])->name('jenis-pakaian.store');
        Route::get('/lihat', [JenisPakaianController::class, 'show'])->name('jenis-pakaian.show');
        Route::get('/ubah', [JenisPakaianController::class, 'edit'])->name('jenis-pakaian.edit');
        Route::post('/ubah', [JenisPakaianController::class, 'update'])->name('jenis-pakaian.update');
        Route::post('/hapus', [JenisPakaianController::class, 'delete'])->name('jenis-pakaian.delete');
        Route::get('/trash', [JenisPakaianController::class, 'trash'])->name('jenis-pakaian.trash');
        Route::post('/pulihkan', [JenisPakaianController::class, 'restore'])->name('jenis-pakaian.restore');
        Route::post('/hapus-permanen', [JenisPakaianController::class, 'destroy'])->name('jenis-pakaian.destroy');
        Route::post('/impor', [JenisPakaianController::class, 'import'])->name('jenis-pakaian.import');
        Route::get('/ekspor', [JenisPakaianController::class, 'export'])->name('jenis-pakaian.export');
    });

    Route::group([
        'prefix' => 'harga-jenis-layanan',
        'middleware' => ['role:owner|manajer_laundry|pic'],
    ], function () {

        Route::get('/', [HargaJenisLayananController::class, 'index'])->name('harga-jenis-layanan');
        Route::post('/tambah', [HargaJenisLayananController::class, 'store'])->name('harga-jenis-layanan.store');
        Route::get('/lihat', [HargaJenisLayananController::class, 'show'])->name('harga-jenis-layanan.show');
        Route::get('/ubah', [HargaJenisLayananController::class, 'edit'])->name('harga-jenis-layanan.edit');
        Route::post('/ubah', [HargaJenisLayananController::class, 'update'])->name('harga-jenis-layanan.update');
        Route::post('/hapus', [HargaJenisLayananController::class, 'delete'])->name('harga-jenis-layanan.delete');
        Route::get('/trash', [HargaJenisLayananController::class, 'trash'])->name('harga-jenis-layanan.trash');
        Route::post('/pulihkan', [HargaJenisLayananController::class, 'restore'])->name('harga-jenis-layanan.restore');
        Route::post('/hapus-permanen', [HargaJenisLayananController::class, 'destroy'])->name('harga-jenis-layanan.destroy');
        Route::post('/impor', [HargaJenisLayananController::class, 'import'])->name('harga-jenis-layanan.import');
        Route::get('/ekspor', [HargaJenisLayananController::class, 'export'])->name('harga-jenis-layanan.export');
    });

    Route::group([
        'prefix' => 'layanan-prioritas',
        'middleware' => ['role:owner|manajer_laundry|pic'],
    ], function () {

        Route::get('/', [LayananPrioritasController::class, 'index'])->name('layanan-prioritas');
        Route::post('/tambah', [LayananPrioritasController::class, 'store'])->name('layanan-prioritas.store');
        Route::get('/lihat', [LayananPrioritasController::class, 'show'])->name('layanan-prioritas.show');
        Route::get('/ubah', [LayananPrioritasController::class, 'edit'])->name('layanan-prioritas.edit');
        Route::post('/ubah', [LayananPrioritasController::class, 'update'])->name('layanan-prioritas.update');
        Route::post('/hapus', [LayananPrioritasController::class, 'delete'])->name('layanan-prioritas.delete');
        Route::get('/trash', [LayananPrioritasController::class, 'trash'])->name('layanan-prioritas.trash');
        Route::post('/pulihkan', [LayananPrioritasController::class, 'restore'])->name('layanan-prioritas.restore');
        Route::post('/hapus-permanen', [LayananPrioritasController::class, 'destroy'])->name('layanan-prioritas.destroy');
        Route::post('/impor', [LayananPrioritasController::class, 'import'])->name('layanan-prioritas.import');
        Route::get('/ekspor', [LayananPrioritasController::class, 'export'])->name('layanan-prioritas.export');
    });

    Route::group([
        'prefix' => 'layanan-tambahan',
        'middleware' => ['role:owner|manajer_laundry|pic'],
    ], function () {

        Route::get('/', [LayananTambahanController::class, 'index'])->name('layanan-tambahan');
        Route::post('/tambah', [LayananTambahanController::class, 'store'])->name('layanan-tambahan.store');
        Route::get('/lihat', [LayananTambahanController::class, 'show'])->name('layanan-tambahan.show');
        Route::get('/ubah', [LayananTambahanController::class, 'edit'])->name('layanan-tambahan.edit');
        Route::post('/ubah', [LayananTambahanController::class, 'update'])->name('layanan-tambahan.update');
        Route::post('/hapus', [LayananTambahanController::class, 'delete'])->name('layanan-tambahan.delete');
        Route::get('/trash', [LayananTambahanController::class, 'trash'])->name('layanan-tambahan.trash');
        Route::post('/pulihkan', [LayananTambahanController::class, 'restore'])->name('layanan-tambahan.restore');
        Route::post('/hapus-permanen', [LayananTambahanController::class, 'destroy'])->name('layanan-tambahan.destroy');
        Route::post('/impor', [LayananTambahanController::class, 'import'])->name('layanan-tambahan.import');
        Route::get('/ekspor', [LayananTambahanController::class, 'export'])->name('layanan-tambahan.export');
    });

    Route::group([
        'prefix' => 'pelanggan',
        'middleware' => ['role:owner|manajer_laundry|pegawai_laundry|pic'],
    ], function () {

        Route::get('/', [PelangganController::class, 'index'])->name('pelanggan');
        Route::post('/tambah', [PelangganController::class, 'store'])->name('pelanggan.store');
        Route::get('/lihat', [PelangganController::class, 'show'])->name('pelanggan.show');
        Route::get('/ubah', [PelangganController::class, 'edit'])->name('pelanggan.edit');
        Route::post('/ubah', [PelangganController::class, 'update'])->name('pelanggan.update');
        Route::post('/hapus', [PelangganController::class, 'delete'])->name('pelanggan.delete');
        Route::post('/impor', [PelangganController::class, 'import'])->name('pelanggan.import');
        Route::get('/ekspor', [PelangganController::class, 'export'])->name('pelanggan.export');
    });

    Route::group([
        'prefix' => 'participant',
        'middleware' => ['role:owner|manajer_laundry|pic'],
    ], function () {

        Route::get('/', [participantController::class, 'index'])->name('participant');
        Route::post('/tambah', [participantController::class, 'store'])->name('participant.store');
        Route::get('/lihat', [participantController::class, 'show'])->name('participant.show');
        Route::get('/ubah', [participantController::class, 'edit'])->name('participant.edit');
        Route::post('/ubah', [participantController::class, 'update'])->name('participant.update');
        Route::post('/hapus', [participantController::class, 'delete'])->name('participant.delete');
        Route::get('/anggota/{detail_participant:kartu_keluarga}', [participantController::class, 'anggota'])->name('participant.anggota');
        Route::get('/detail-anggota', [participantController::class, 'detailAnggota'])->name('participant.anggota.show');
        Route::post('/impor', [participantController::class, 'import'])->name('participant.import');
    });

    Route::group([
        'prefix' => 'transaksi',
        'middleware' => ['role:owner|manajer_laundry|pegawai_laundry|pic'],
    ], function () {

        Route::group([
            'prefix' => 'owner',
            'middleware' => ['role:owner|pic'],
        ], function () {

            Route::get('/', [TransaksiController::class, 'index'])->name('transaksi.owner');
            Route::get('/{cabang:slug}', [TransaksiController::class, 'indexCabang'])->name('transaksi.owner.cabang');
            Route::get('/{cabang:slug}/jadwal', [TransaksiController::class, 'indexCabangJadwal'])->name('transaksi.owner.cabang.jadwal');
            Route::get('/{cabang:slug}/lihat/{transaksi:id}', [TransaksiController::class, 'viewDetailTransaksi'])->name('transaksi.owner.view');
            Route::get('/{cabang:slug}/lihat/{transaksi:id}/layanan', [DetailLayananTransaksiController::class, 'viewDetailLayanan'])->name('transaksi.owner.view.layanan');
            Route::get('/{cabang:slug}/tambah', [TransaksiController::class, 'createTransaksiCabang'])->name('transaksi.owner.cabang.create');
            Route::post('/{cabang:slug}/tambah', [TransaksiController::class, 'storeTransaksiCabang'])->name('transaksi.owner.cabang.store');
            Route::get('/{cabang:slug}/ubah-jenis-pakaian', [TransaksiController::class, 'ubahJenisPakaian'])->name('transaksi.owner.cabang.create.ubahJenisPakaian');
            Route::get('/{cabang:slug}/ubah-jenis-layanan', [TransaksiController::class, 'ubahJenisLayanan'])->name('transaksi.owner.cabang.create.ubahJenisLayanan');
            Route::get('/{cabang:slug}/ubah-layanan-tambahan', [TransaksiController::class, 'ubahLayananTambahan'])->name('transaksi.owner.cabang.create.ubahLayananTambahan');
            Route::get('/{cabang:slug}/hitung-total-bayar', [TransaksiController::class, 'hitungTotalBayar'])->name('transaksi.owner.cabang.create.hitungTotalBayar');
            Route::get('/{cabang:slug}/ubah/{transaksi:id}', [TransaksiController::class, 'editTransaksiCabang'])->name('transaksi.owner.cabang.edit');
            Route::post('/{cabang:slug}/ubah/{transaksi:id}', [TransaksiController::class, 'updateTransaksiCabang'])->name('transaksi.owner.cabang.update');
            Route::get('/{cabang:slug}/ubah-status', [TransaksiController::class, 'editStatusTransaksiCabang'])->name('transaksi.owner.cabang.edit.status');
            Route::post('/{cabang:slug}/ubah-status', [TransaksiController::class, 'updateStatusTransaksiCabang'])->name('transaksi.owner.cabang.update.status');
            Route::post('/{cabang:slug}/hapus', [TransaksiController::class, 'deleteTransaksiCabang'])->name('transaksi.owner.cabang.delete');
            Route::post('/{cabang:slug}/konfirmasi-upah-participant', [TransaksiController::class, 'konfirmasiUpah'])->name('transaksi.owner.cabang.konfirmasiUpah');
        });

        Route::get('/', [TransaksiController::class, 'index'])->name('transaksi');
        Route::get('/jadwal', [TransaksiController::class, 'indexJadwal'])->name('transaksi.jadwal');
        Route::get('/lihat/{transaksi:id}', [TransaksiController::class, 'viewDetailTransaksi'])->name('transaksi.view');
        Route::get('/lihat/{transaksi:id}/layanan', [DetailLayananTransaksiController::class, 'viewDetailLayanan'])->name('transaksi.view.layanan');
        Route::get('/tambah', [TransaksiController::class, 'createTransaksiCabang'])->name('transaksi.create');
        Route::post('/tambah', [TransaksiController::class, 'storeTransaksiCabang'])->name('transaksi.store');
        Route::get('/ubah-jenis-pakaian', [TransaksiController::class, 'ubahJenisPakaian'])->name('transaksi.create.ubahJenisPakaian');
        Route::get('/ubah-jenis-layanan', [TransaksiController::class, 'ubahJenisLayanan'])->name('transaksi.create.ubahJenisLayanan');
        Route::get('/ubah-layanan-tambahan', [TransaksiController::class, 'ubahLayananTambahan'])->name('transaksi.create.ubahLayananTambahan');
        Route::get('/hitung-total-bayar', [TransaksiController::class, 'hitungTotalBayar'])->name('transaksi.create.hitungTotalBayar');
        Route::get('/ubah/{transaksi:id}', [TransaksiController::class, 'editTransaksiCabang'])->name('transaksi.edit');
        Route::post('/ubah/{transaksi:id}', [TransaksiController::class, 'updateTransaksiCabang'])->name('transaksi.update');
        Route::get('/ubah-status', [TransaksiController::class, 'editStatusTransaksiCabang'])->name('transaksi.edit.status');
        Route::post('/ubah-status', [TransaksiController::class, 'updateStatusTransaksiCabang'])->name('transaksi.update.status');
        Route::post('/hapus', [TransaksiController::class, 'deleteTransaksiCabang'])->name('transaksi.delete');
        Route::post('/konfirmasi-upah-participant', [TransaksiController::class, 'konfirmasiUpah'])->name('transaksi.konfirmasiUpah');

        Route::get('/cetak-struk/{transaksi:id}', [TransaksiController::class, 'cetakStrukTransaksi'])->name('transaksi.cetak-struk');
    });

    Route::group([
        'prefix' => 'monitoring-program-participant',
        'middleware' => ['role:owner|manajer_laundry|pic'],
    ], function () {

        Route::get('/', [MonitoringparticipantController::class, 'index'])->name('monitoring');
        Route::post('/perbarui-data-monitoring', [MonitoringparticipantController::class, 'perbaruiDataMonitoring'])->name('monitoring.update.data');
        Route::post('/reset-data-monitoring', [MonitoringparticipantController::class, 'resetDataMonitoring'])->name('monitoring.reset.data');

        Route::get('/ubah-pemasukkan', [MonitoringparticipantController::class, 'editPemasukkan'])->name('monitoring.edit.pemasukkan');
        Route::post('/ubah-pemasukkan', [MonitoringparticipantController::class, 'updatePemasukkan'])->name('monitoring.update.pemasukkan');
        Route::get('/riwayat-pendapatan/{participant:id}', [MonitoringparticipantController::class, 'riwayatPendapatan'])->name('monitoring.participant.riwayat');
    });

    Route::group([
        'prefix' => 'monitoring-participant',
        'middleware' => ['role:rw'],
    ], function () {

        Route::get('/', [MonitoringparticipantController::class, 'indexRw'])->name('monitoring.rw');
        Route::post('/pdf', [MonitoringparticipantController::class, 'pdfMonitoringparticipantRw'])->name('monitoring.rw.pdf');
    });

    Route::group([
        'prefix' => 'transaksi-participant',
        'middleware' => ['role:participant'],
    ], function () {

        Route::get('/', [TransaksiController::class, 'transaksiparticipantHarian'])->name('transaksi-participant');
        Route::get('/semua', [TransaksiController::class, 'transaksiparticipantSemua'])->name('transaksi-participant.semua');
        Route::get('/lihat/{transaksi:id}', [TransaksiController::class, 'viewDetailTransaksiparticipant'])->name('transaksi-participant.view');
        Route::get('/lihat/{transaksi:id}/layanan', [DetailLayananTransaksiController::class, 'viewDetailLayananparticipant'])->name('transaksi-participant.view.layanan');
    });

    Route::group([
        'prefix' => 'laporan',
        'middleware' => ['role:owner|manajer_laundry|pic'],
    ], function () {

        Route::get('/pendapatan-laundry', [LaporanController::class, 'laporanPendapatanLaundry'])->name('laporan.pendapatan.laundry');
        Route::post('/pendapatan-laundry/pdf', [LaporanController::class, 'pdfLaporanPendapatanLaundry'])->name('laporan.pendapatan.laundry.pdf');

        Route::get('/pendapatan-participant', [LaporanController::class, 'laporanPendapatanparticipant'])->name('laporan.pendapatan.participant');
        Route::post('/pendapatan-participant/pdf', [LaporanController::class, 'pdfLaporanPendapatanparticipant'])->name('laporan.pendapatan.participant.pdf');

        Route::get('/pelanggan', [LaporanController::class, 'laporanPelanggan'])->name('laporan.pelanggan');
        Route::post('/pelanggan/pdf', [LaporanController::class, 'pdfLaporanPelanggan'])->name('laporan.pelanggan.pdf');

        Route::get('/participant', [LaporanController::class, 'laporanparticipant'])->name('laporan.participant');
        Route::post('/participant/pdf', [LaporanController::class, 'pdfLaporanparticipant'])->name('laporan.participant.pdf');
    });
});

require __DIR__ . '/auth.php';
