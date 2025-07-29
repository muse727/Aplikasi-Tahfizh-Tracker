<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgressController; // Jangan lupakan baris ini
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route ini akan otomatis memiliki awalan /api di depannya.
// Jadi URL lengkapnya akan menjadi /api/modules/{course}



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


