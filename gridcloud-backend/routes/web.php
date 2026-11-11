<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

/*
 * Serve file upload dari storage/app/public secara langsung.
 *
 * Diperlukan karena php artisan serve di Windows tidak bisa mengikuti
 * symlink junction, sehingga /storage/* yang sebelumnya served via symlink
 * harus di-handle oleh Laravel route ini.
 *
 * URL contoh: GET /storage/uploads/namafile.pdf
 * file_path di DB: uploads/namafile.pdf  (format dari Storage::store())
 *
 * server.php di root project memastikan request ini diteruskan ke Laravel
 * dan tidak di-short-circuit oleh PHP built-in server.
 */
Route::get('/storage/{subfolder}/{filename}', function (string $subfolder, string $filename) {
    // Cegah path traversal
    if (str_contains($subfolder, '..') || str_contains($filename, '..') ||
        str_contains($subfolder, "\0") || str_contains($filename, "\0")) {
        abort(400);
    }

    $path = $subfolder . '/' . $filename;

    if (!Storage::disk('public')->exists($path)) {
        abort(404, 'File tidak ditemukan.');
    }

    $fullPath = Storage::disk('public')->path($path);
    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

    return response()->file($fullPath, [
        'Content-Type'              => $mimeType,
        'Content-Disposition'       => 'attachment; filename="' . $filename . '"',
        'Access-Control-Allow-Origin'  => '*',
        'Access-Control-Allow-Headers' => '*',
    ]);
});
