<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Artisan;
use Cache;
use Carbon\Carbon;
use CoreComponentRepository;
use DB;

class AdminController extends Controller
{

    public function admin_dashboard(Request $request)
    {
        return view('backend.dashboard');
    }

    function clearCache(Request $request)
    {
        Artisan::call('optimize:clear');
        flash(translate('Cache cleared successfully'))->success();
        return back();
    }
}
