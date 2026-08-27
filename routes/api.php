<?php

use App\Http\Controllers\Api\V1\StructuredDocumentController;
use App\Http\Controllers\CapabilityController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/', CapabilityController::class);
Route::get('/__verify', [CapabilityController::class, 'verify']);
Route::get('/health', HealthController::class)->name('health');

Route::middleware('service')->group(function (): void {
    Route::middleware('throttle:structured-data-reads')->group(function (): void {
        Route::get('/v1/projects/{project}/documents', [StructuredDocumentController::class, 'index']);
        Route::get('/v1/projects/{project}/documents/{document}', [StructuredDocumentController::class, 'show']);
    });
    Route::middleware('throttle:structured-data-writes')->group(function (): void {
        Route::put('/v1/projects/{project}/documents/{document}', [StructuredDocumentController::class, 'update']);
        Route::delete('/v1/projects/{project}/documents/{document}', [StructuredDocumentController::class, 'destroy']);
    });
});

Route::patterns([
    'project' => '[A-Za-z0-9][A-Za-z0-9_-]{0,99}',
    'document' => '[A-Za-z0-9][A-Za-z0-9_-]{0,99}',
]);
