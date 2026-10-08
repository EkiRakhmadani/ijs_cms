<?php

use App\Http\Controllers\Api\V1\ServiceController;
use Illuminate\Support\Facades\Route;

/*
| Public, read-only content API for the Next.js frontend (ijs_frontend).
| Versioned so the frontend can keep consuming v1 while a later shape lands
| alongside it rather than breaking underneath it.
*/
Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('services/{service}', [ServiceController::class, 'show'])->name('services.show');
});
