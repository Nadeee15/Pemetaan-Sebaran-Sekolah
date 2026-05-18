<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SekolahController;

// Halaman utama - peta SIG
Route::get('/', [SekolahController::class, 'index'])->name('peta.index');

// API Routes untuk Leaflet
Route::prefix('api')->group(function () {
    Route::get('/sekolah/geojson', [SekolahController::class, 'getGeoJSON'])->name('api.sekolah.geojson');
    Route::get('/sekolah/radius', [SekolahController::class, 'getRadius'])->name('api.sekolah.radius');
    Route::get('/sekolah/statistik', [SekolahController::class, 'getStatistikApi'])->name('api.sekolah.statistik');
    Route::get('/sekolah/kota', [SekolahController::class, 'getKota'])->name('api.sekolah.kota');
});
