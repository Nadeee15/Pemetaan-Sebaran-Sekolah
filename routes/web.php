<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SekolahController;

// Halaman utama - peta SIG
Route::get('/', [SekolahController::class, 'index'])->name('peta.index');

// API Routes untuk Leaflet
Route::prefix('api')->group(function () {
    Route::get('/sekolah', [SekolahController::class, 'getGeoJSON'])->name('api.sekolah.geojson');
    Route::get('/sekolah/radius', [SekolahController::class, 'getRadius'])->name('api.sekolah.radius');
    Route::get('/sekolah/statistik', [SekolahController::class, 'getStatistikApi'])->name('api.sekolah.statistik');
    Route::get('/sekolah/kota', [SekolahController::class, 'getKota'])->name('api.sekolah.kota');

    // Bantuan routes
    Route::get('/bantuan', [SekolahController::class, 'getBantuan'])->name('api.bantuan');
    Route::get('/prioritas-bantuan', [SekolahController::class, 'getPrioritasBantuan'])->name('api.prioritas-bantuan');
    Route::get('/jalur-bantuan', [SekolahController::class, 'getJalurBantuan'])->name('api.jalur-bantuan');
});
