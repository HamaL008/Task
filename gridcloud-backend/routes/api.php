<?php

use App\Http\Controllers\AssignmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\TelegramBotController;

Route::middleware('api')->group(function () {
    // Tugas
    Route::resource('assignments', AssignmentController::class)->only(['index', 'store']);
    Route::delete('assignments/{id}',              [AssignmentController::class, 'destroy']);
    Route::get('assignments/{id}/submissions',      [AssignmentController::class, 'getSubmissions']);
    Route::put('assignments/{id}',                  [AssignmentController::class, 'update']);
    Route::patch('assignments/{id}/close',          [AssignmentController::class, 'close']);
    Route::patch('assignments/{id}/reopen',         [AssignmentController::class, 'reopen']);

    // Pengumpulan
    Route::post('submissions', [SubmissionController::class, 'store']);

    // Webhook Telegram
    Route::post('telegram/webhook', [TelegramBotController::class, 'handleWebhook']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
