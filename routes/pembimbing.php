<?php

use App\Http\Controllers\Pembimbing\DashboardController;
use App\Http\Controllers\Pembimbing\ExcelController;
use App\Http\Controllers\Pembimbing\PesertaPklController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pembimbing\PesertaAccountController;

Route::middleware(['auth', 'role:pembimbing'])
    ->prefix('pembimbing')
    ->name('pembimbing.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/peserta', [PesertaPklController::class, 'index'])->name('peserta.index');
        Route::get('/peserta/create', [PesertaPklController::class, 'create'])->name('peserta.create');
        Route::post('/peserta', [PesertaPklController::class, 'store'])->name('peserta.store');
        Route::get('/peserta/{peserta}', [PesertaPklController::class, 'show'])->name('peserta.show');
        Route::get('/peserta/{peserta}/edit', [PesertaPklController::class, 'edit'])->name('peserta.edit');
        Route::put('/peserta/{peserta}', [PesertaPklController::class, 'update'])->name('peserta.update');
        Route::delete('/peserta/{peserta}', [PesertaPklController::class, 'destroy'])->name('peserta.destroy');
        Route::get('/excel', [ExcelController::class, 'index'])->name('excel.index');
        Route::get('/excel/export', [ExcelController::class, 'export'])->name('excel.export');
        Route::post('/excel/import', [ExcelController::class, 'import'])->name('excel.import');
        
        Route::post('/peserta/{peserta}/buat-akun',
    [PesertaAccountController::class, 'store']
)->name('peserta.buatAkun');

Route::post('/peserta/{peserta}/reset-password', [PesertaAccountController::class, 'resetPassword'])
    ->name('peserta.resetPassword');
    });

