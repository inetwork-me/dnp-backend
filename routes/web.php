<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['prevent-back-history','handle-demo-login']], function () {
    Auth::routes(['verify' => true]);
});

Route::get('/', [AdminController::class, 'admin_dashboard'])->name('admin.dashboard')->middleware(['auth','admin', 'prevent-back-history']);
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
