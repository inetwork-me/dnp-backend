<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\V1\MpgsPaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['prevent-back-history','handle-demo-login']], function () {
    Auth::routes(['verify' => true]);
});

// Admin dashboard moved to API - redirect to API documentation or login
Route::get('/', function () {
    if (auth()->check()) {
        return response()->json(['message' => 'Admin functionality available via API V2 - see API_DOCUMENTATION.md']);
    }
    return redirect()->route('login');
})->name('admin.dashboard');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// MPGS Payment Redirects (web routes for redirects from MPGS)
Route::prefix('api/payment/mpgs')->group(function () {
    Route::get('success', [MpgsPaymentController::class, 'success'])->name('mpgs.success');
    Route::get('cancel', [MpgsPaymentController::class, 'cancel'])->name('mpgs.cancel');
});
