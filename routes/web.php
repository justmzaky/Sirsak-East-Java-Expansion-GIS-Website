<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GisController;
use App\Http\Controllers\Admin\WasteCollectionController;
use App\Http\Controllers\Admin\AgregatorController;
use App\Http\Controllers\Admin\RecyclerController;
use App\Http\Controllers\SuperAdmin\EntitasController;
use App\Http\Controllers\SuperAdmin\PenimbanganController;
use App\Http\Controllers\SuperAdmin\PengirimanController;
use Illuminate\Support\Facades\Route;

// ─── AUTH ────────────────────────────────────────────────────────
Route::get('/',  [LoginController::class, 'showLoginForm'])->name('home');
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ─── AUTHENTICATED ROUTES ─────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard & GIS — accessible by both roles
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/gis', [GisController::class, 'index'])->name('gis.index');
    Route::get('/gis/data', [GisController::class, 'mapData'])->name('gis.data');

    // Monitoring pages — both roles
    Route::get('/waste-collection', [WasteCollectionController::class, 'index'])->name('waste-collection.index');
    Route::get('/agregator', [AgregatorController::class, 'index'])->name('agregator.index');
    Route::get('/agregator/{aggregator}', [AgregatorController::class, 'show'])->name('agregator.show');
    Route::get('/recycler', [RecyclerController::class, 'index'])->name('recycler.index');
    Route::get('/recycler/{recycler}', [RecyclerController::class, 'show'])->name('recycler.show');

    // ─── SUPERADMIN ONLY ──────────────────────────────────────────
    Route::middleware(['role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {

        // Entitas — Aggregator
        Route::get('/entitas/aggregator', [EntitasController::class, 'aggregatorIndex'])->name('entitas.aggregator.index');
        Route::get('/entitas/aggregator/create', [EntitasController::class, 'aggregatorCreate'])->name('entitas.aggregator.create');
        Route::post('/entitas/aggregator', [EntitasController::class, 'aggregatorStore'])->name('entitas.aggregator.store');
        Route::get('/entitas/aggregator/{aggregator}/edit', [EntitasController::class, 'aggregatorEdit'])->name('entitas.aggregator.edit');
        Route::put('/entitas/aggregator/{aggregator}', [EntitasController::class, 'aggregatorUpdate'])->name('entitas.aggregator.update');
        Route::delete('/entitas/aggregator/{aggregator}', [EntitasController::class, 'aggregatorDestroy'])->name('entitas.aggregator.destroy');

        // Entitas — BSU
        Route::get('/entitas/bsu', [EntitasController::class, 'bsuIndex'])->name('entitas.bsu.index');
        Route::get('/entitas/bsu/create', [EntitasController::class, 'bsuCreate'])->name('entitas.bsu.create');
        Route::post('/entitas/bsu', [EntitasController::class, 'bsuStore'])->name('entitas.bsu.store');
        Route::get('/entitas/bsu/{wasteUnit}/edit', [EntitasController::class, 'bsuEdit'])->name('entitas.bsu.edit');
        Route::put('/entitas/bsu/{wasteUnit}', [EntitasController::class, 'bsuUpdate'])->name('entitas.bsu.update');
        Route::delete('/entitas/bsu/{wasteUnit}', [EntitasController::class, 'bsuDestroy'])->name('entitas.bsu.destroy');

        // Entitas — Recycler
        Route::get('/entitas/recycler', [EntitasController::class, 'recyclerIndex'])->name('entitas.recycler.index');
        Route::get('/entitas/recycler/create', [EntitasController::class, 'recyclerCreate'])->name('entitas.recycler.create');
        Route::post('/entitas/recycler', [EntitasController::class, 'recyclerStore'])->name('entitas.recycler.store');
        Route::get('/entitas/recycler/{recycler}/edit', [EntitasController::class, 'recyclerEdit'])->name('entitas.recycler.edit');
        Route::put('/entitas/recycler/{recycler}', [EntitasController::class, 'recyclerUpdate'])->name('entitas.recycler.update');
        Route::delete('/entitas/recycler/{recycler}', [EntitasController::class, 'recyclerDestroy'])->name('entitas.recycler.destroy');

        // Penimbangan
        Route::get('/penimbangan', [PenimbanganController::class, 'index'])->name('penimbangan.index');
        Route::post('/penimbangan', [PenimbanganController::class, 'store'])->name('penimbangan.store');
        Route::delete('/penimbangan/{collection}', [PenimbanganController::class, 'destroy'])->name('penimbangan.destroy');

        // Pengiriman
        Route::get('/pengiriman', [PengirimanController::class, 'index'])->name('pengiriman.index');
        Route::post('/pengiriman', [PengirimanController::class, 'store'])->name('pengiriman.store');
        // Laporan
        Route::get('/laporan', [\App\Http\Controllers\SuperAdmin\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export/penimbangan', [\App\Http\Controllers\SuperAdmin\LaporanController::class, 'exportPenimbangan'])->name('laporan.export.penimbangan');
        Route::get('/laporan/export/pengiriman', [\App\Http\Controllers\SuperAdmin\LaporanController::class, 'exportPengiriman'])->name('laporan.export.pengiriman');
        Route::get('/laporan/export/material-flow', [\App\Http\Controllers\SuperAdmin\LaporanController::class, 'exportMaterialFlow'])->name('laporan.export.material-flow');
        Route::get('/laporan/export/recycler', [\App\Http\Controllers\SuperAdmin\LaporanController::class, 'exportRecycler'])->name('laporan.export.recycler');
        Route::get('/laporan/export/entitas', [\App\Http\Controllers\SuperAdmin\LaporanController::class, 'exportEntitas'])->name('laporan.export.entitas');
        Route::get('/laporan/export/aktivitas', [\App\Http\Controllers\SuperAdmin\LaporanController::class, 'exportAktivitas'])->name('laporan.export.aktivitas');

        Route::get('/pengiriman/stock/{aggregator}', [PengirimanController::class, 'getAggregatorStock'])->name('pengiriman.stock');
        Route::patch('/pengiriman/{shipment}/status', [PengirimanController::class, 'updateStatus'])->name('pengiriman.updateStatus');
    });
});
