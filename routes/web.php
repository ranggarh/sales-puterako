<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenawaranController;

Route::get('/', function () {
    return view('layouts.app');
});

// Penawaran routes
Route::prefix('penawaran')->group(function () {
    Route::get('/list', [PenawaranController::class, 'index'])->name('penawaran.list');
    Route::post('/tambah-penawaran', [PenawaranController::class, 'store'])->name('penawaran.store');
    Route::get('/follow-up', [PenawaranController::class, 'followUp'])->name('penawaran.followup');
    Route::get('/rekap-survey', [PenawaranController::class, 'rekapSurvey'])->name('penawaran.rekap-survey');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');