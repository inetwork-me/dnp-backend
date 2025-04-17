<?php

use App\Http\Controllers\AdminController;

Route::get('/admin', [AdminController::class, 'admin_dashboard'])->name('admin.dashboard')->middleware([ 'admin', 'prevent-back-history']);

