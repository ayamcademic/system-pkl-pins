<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pembimbing\PesertaPklController;

use App\Http\Controllers\Peserta\DashboardController;
use App\Http\Controllers\Peserta\AttendanceController;
use App\Http\Controllers\Peserta\DailyReportController;
use App\Http\Controllers\Peserta\WeeklyReportController;
use App\Http\Controllers\Pembimbing\PesertaAccountController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = request()->user();

    if ($user->role === 'pembimbing') {
        return redirect()->route('loading'); // return redirect()->route('pembimbing.peserta.index'); ke /pembimbing/peserta, aku maunya dia ke /pembimbing/dashboard YEY BISA
    }

    // return view('dashboard'); // peserta / default
    return redirect()->route('peserta.dashboard');
})->name('dashboard');

require __DIR__.'/pembimbing.php';

Route::get('/loading', function () {
    return view('loading');
})->name('loading');

Route::get('/peserta/export/pdf', [PesertaPklController::class, 'exportPdf'])
    ->name('pembimbing.peserta.export.pdf');



Route::middleware(['auth', 'role:peserta'])
    ->prefix('peserta')
    ->name('peserta.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/absensi', [AttendanceController::class, 'index'])->name('absensi');
        Route::post('/absensi/masuk', [AttendanceController::class, 'masuk'])->name('absensi.masuk');
        Route::post('/absensi/pulang', [AttendanceController::class, 'pulang'])->name('absensi.pulang');

        Route::get('/laporan', [DailyReportController::class, 'index'])->name('laporan');
        Route::post('/laporan', [DailyReportController::class, 'store'])->name('laporan.store');

        Route::get('/rekap', [WeeklyReportController::class, 'index'])->name('rekap');
    });

// Route::middleware(['auth', 'role:pembimbing'])->prefix('pembimbing')->name('pembimbing.')->group(function () {
//     Route::post('/peserta/{peserta}/buat-akun', [PesertaAccountController::class, 'store'])
//         ->name('peserta.buatAkun');
// });

// Route::prefix('pembimbing')
//     ->name('pembimbing.')
//     ->middleware(['auth'])
//     ->group(function () {

//         Route::post('/peserta/{peserta}/buat-akun',
//             [PesertaAccountController::class, 'store']
//         )->name('peserta.buatAkun');

// });