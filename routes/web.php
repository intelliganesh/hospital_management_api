<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\InvoiceController;
use App\Models\SystemSettings;
use Illuminate\Support\Facades\Route;

Route::get('/', action: function () {
    return redirect('documentation');
});
Route::get('documentation', [AuthController::class, 'documentation']);
Route::get('consultation-test', function () {
    return view('templates.downloads.consultation-test');
});
Route::get('test', function () {
    return view('templates.downloads.test');
});
Route::get('invoice-bill', function () {
    return view('templates.downloads.invoice-bill');
});
Route::get('/test-logo', function () {

    $letter_header_address = SystemSettings::get()[0]->letter_header;

    return view('test_logo', compact('letter_header_address'));
});

Route::get("consultation-prescription/{id}", [InvoiceController::class, 'downloadPrescriptionweb']);
// Route::get("consultation-prescription/{id}", [InvoiceController::class, 'downloadPrescription']);
Route::get("invoice-bill/details/{id}", [InvoiceController::class, 'test']);
Route::get('/documentation/logs', [AuthController::class, 'index'])->name('/documentation/logs');
Route::get('/document/download', [DocumentDownloadController::class, 'index'])->name('/document/download');
