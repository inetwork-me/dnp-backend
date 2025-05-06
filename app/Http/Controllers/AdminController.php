<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Artisan;
use Cache;
use Carbon\Carbon;
use CoreComponentRepository;
use DB;

class AdminController extends Controller
{

    public function admin_dashboard(Request $request)
    {
        $root_categories = Category::where('level', 0)->get();

        $data['root_categories'] = $root_categories;

        $data['total_customers'] = User::where('user_type', 'customer')->where('email_verified_at', '!=', null)->count();
        $data['total_products'] = Product::where('approved', 1)->where('published', 1)->count();
        $data['total_inhouse_products'] = Product::where('approved', 1)->where('published', 1)->where('added_by', 'admin')->count();
        $data['total_sellers_products'] = Product::where('approved', 1)->where('published', 1)->where('added_by', '!=', 'admin')->count();
        $data['total_categories'] = Category::count();

        $data['total_brands'] = Brand::count();

        $data['total_sellers'] = User::where('user_type', 'seller')->where('email_verified_at', '!=', null)->count();
        
        $data['inhouse_product_rating'] = Product::where('added_by', 'admin')->where('rating', '!=', 0)->avg('rating');

        $data['dashboard_statistics'] = [
            [
                'icon' => '<i class="fa-solid fa-users" style="font-size: 20px;margin:0 10px;background: #ECDAF4;border-radius: 12px;padding: 10px;color: #6f2987;"></i>',
                'name' => translate('Total Customers'),
                'count' => $data['total_customers'],
                'class' => 'col-lg-4'
            ],
            [
                'icon' => '<i class="fa-solid fa-box-archive" style="font-size: 20px;margin:0 10px;background: #ECDAF4;border-radius: 12px;padding: 10px;color: #6f2987;"></i>',
                'name' => translate('Total Products'),
                'count' => $data['total_products'],
                'class' => 'col-lg-4'
            ],
            [
                'icon' => '<i class="fa-solid fa-box-archive" style="font-size: 20px;margin:0 10px;background: #ECDAF4;border-radius: 12px;padding: 10px;color: #6f2987;"></i>',
                'name' => translate('Total Inhouse Products'),
                'count' => $data['total_inhouse_products'],
                'class' => 'col-lg-4'
            ],
            [
                'icon' => '<i class="fa-solid fa-box-archive" style="font-size: 20px;margin:0 10px;background: #ECDAF4;border-radius: 12px;padding: 10px;color: #6f2987;"></i>',
                'name' => translate('Total Categories'),
                'count' => $data['total_categories'],
                'class' => 'col-lg-4'
            ],
            [
                'icon' => '<i class="fa-solid fa-tags" style="font-size: 20px;margin:0 10px;background: #ECDAF4;border-radius: 12px;padding: 10px;color: #6f2987;"></i>',
                'name' => translate('Total Brands'),
                'count' => $data['total_brands'],
                'class' => 'col-lg-4'
            ],
        ];


        return view('backend.dashboard', $data);
    }

    function clearCache(Request $request)
    {
        Artisan::call('optimize:clear');
        flash(translate('Cache cleared successfully'))->success();
        return back();
    }
}
