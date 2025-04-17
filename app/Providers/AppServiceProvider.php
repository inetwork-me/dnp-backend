<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
      Schema::defaultStringLength(191);
      Paginator::useBootstrap();    
      try {
          // $settings = \App\Models\BusinessSetting::where('type', 'shipping_add_token_oto')->first();
          // if (!empty($settings)) {
          //     config(['oto.refresh_token' => $settings->value]);
          // }
      } catch (\Exception $e) {
          // Log the error or handle it accordingly
          \Log::error('Database connection error: ' . $e->getMessage());
      }
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }
}
