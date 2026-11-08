<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\InteractionController;
use App\Http\Controllers\Api\DashboardController;

// Tes API (Bersifat publik untuk pemeriksaan awal)
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Laravel F-6 berhasil!',
        'data' => [
            'project' => 'CRM Klien dan Kontak',
            'status' => 'Berjalan',
        ],
    ]);
});

// API CRM F-6 (Dilindungi oleh middleware verifikasi token F-1)
// Catatan: Ganti 'auth:sanctum' dengan nama middleware kustom F-1 kelompokmu jika ada (misal: 'f1.auth')
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Klien
    Route::get('/clients', [ClientController::class, 'index']);
    Route::post('/clients', [ClientController::class, 'store']);
    Route::get('/clients/{clientId}', [ClientController::class, 'show']);
    Route::put('/clients/{clientId}', [ClientController::class, 'update']);
    Route::patch('/clients/{clientId}/status', [ClientController::class, 'updateStatus']);

    // Kontak Global
    Route::get('/contacts', [ContactController::class, 'indexAll']);
    Route::post('/contacts', [ContactController::class, 'storeGlobal']);
    Route::get('/contacts/{contactId}', [ContactController::class, 'show']);
    Route::put('/contacts/{contactId}', [ContactController::class, 'updateGlobal']);
    Route::patch('/contacts/{contactId}/status', [ContactController::class, 'updateStatusGlobal']);
    Route::delete('/contacts/{contactId}', [ContactController::class, 'destroyGlobal']);

    // Kontak Scoped per Klien
    Route::get('/clients/{clientId}/contacts', [ContactController::class, 'index']);
    Route::post('/clients/{clientId}/contacts', [ContactController::class, 'store']);
    Route::put('/clients/{clientId}/contacts/{contactId}', [ContactController::class, 'update']);
    Route::patch('/clients/{clientId}/contacts/{contactId}/status', [ContactController::class, 'updateStatus']);

    // Riwayat Interaksi Global
    Route::get('/interactions', [InteractionController::class, 'indexAll']);
    Route::post('/interactions', [InteractionController::class, 'storeGlobal']);
    Route::get('/interactions/{id}', [InteractionController::class, 'show']);
    Route::delete('/interactions/{id}', [InteractionController::class, 'destroyGlobal']);

    // Riwayat interaksi Scoped per Klien
    Route::get('/clients/{clientId}/interactions', [InteractionController::class, 'index']);
    Route::post('/clients/{clientId}/interactions', [InteractionController::class, 'store']);
});