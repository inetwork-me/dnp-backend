<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $root_categories = Category::where('level', 0)->get();
        $data['root_categories'] = $root_categories;

        // === Weekly Comparison for Customers ===
        $now = Carbon::now();
        $thisWeekStart = $now->copy()->startOfWeek();
        $thisWeekEnd = $now->copy()->endOfWeek();

        $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
        $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();

        $thisWeekCustomers = \App\Models\User::where('user_type', 'customer')
            ->where('email_verified_at', '!=', null)
            ->whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])
            ->count();

        $lastWeekCustomers = \App\Models\User::where('user_type', 'customer')
            ->where('email_verified_at', '!=', null)
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->count();

        if ($lastWeekCustomers > 0) {
            $customerChangePercent = (($thisWeekCustomers - $lastWeekCustomers) / $lastWeekCustomers) * 100;
        } else {
            $customerChangePercent = $thisWeekCustomers > 0 ? 100 : 0;
        }

        $data['customer_change'] = [
            'percent' => number_format(abs($customerChangePercent), 1),
            'direction' => $customerChangePercent >= 0 ? 'up' : 'down',
            'label' => 'Last Week',
        ];

        // === Existing Counts ===
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
                'change' => $data['customer_change'],
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
}
