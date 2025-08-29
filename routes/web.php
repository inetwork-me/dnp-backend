<?php

use App\Http\Controllers\AdminController;
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
