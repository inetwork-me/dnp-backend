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
        $now = \Carbon\Carbon::now();
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
                'icon' => '<div class="statistics_icon">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.5 21a2.5 2.5 0 0 0 2.5-2.5c0-2.327-1.952-3.301-4-3.708M15 11a4 4 0 0 0 0-8M3.5 21h11a2.5 2.5 0 0 0 2.5-2.5c0-4.08-6-4-8-4s-8-.08-8 4A2.5 2.5 0 0 0 3.5 21M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0" stroke="#6f2987" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                           </div>',
                'name' => 'Total Customers',
                'count' => $data['total_customers'],
                'change' => $data['customer_change'],
                'class' => 'col-lg-4'
            ],
            [
                'icon' =>   '<div class="statistics_icon">
                                <svg width="30" height="30" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m2.401 6.39-.497-.056zm-.778 7 .497.055zm11.754 0-.497.055zm-.778-7 .497-.056zM1.904 6.334l-.778 7 .994.11.778-7zM2.617 15h9.766v-1H2.617zm11.257-1.666-.778-7-.994.11.778 7zM11.604 5H3.396v1h8.21zm1.492 1.334A1.5 1.5 0 0 0 11.605 5v1a.5.5 0 0 1 .497.445zM12.383 15a1.5 1.5 0 0 0 1.49-1.666l-.993.11a.5.5 0 0 1-.497.556zM1.126 13.334A1.5 1.5 0 0 0 2.617 15v-1a.5.5 0 0 1-.497-.555zm1.772-6.89A.5.5 0 0 1 3.395 6V5a1.5 1.5 0 0 0-1.49 1.334zM5 4v-.5H4V4zm5-.5V4h1v-.5zM7.5 1A2.5 2.5 0 0 1 10 3.5h1A3.5 3.5 0 0 0 7.5 0zM5 3.5A2.5 2.5 0 0 1 7.5 1V0A3.5 3.5 0 0 0 4 3.5z" fill="#6f2987"/></svg>
                            </div>',
                'name' => 'Total Products',
                'count' => $data['total_products'],
                'class' => 'col-lg-4'
            ],
            [
                'icon' =>   '<div class="statistics_icon">
                                <svg width="30" height="30" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m2.401 6.39-.497-.056zm-.778 7 .497.055zm11.754 0-.497.055zm-.778-7 .497-.056zM1.904 6.334l-.778 7 .994.11.778-7zM2.617 15h9.766v-1H2.617zm11.257-1.666-.778-7-.994.11.778 7zM11.604 5H3.396v1h8.21zm1.492 1.334A1.5 1.5 0 0 0 11.605 5v1a.5.5 0 0 1 .497.445zM12.383 15a1.5 1.5 0 0 0 1.49-1.666l-.993.11a.5.5 0 0 1-.497.556zM1.126 13.334A1.5 1.5 0 0 0 2.617 15v-1a.5.5 0 0 1-.497-.555zm1.772-6.89A.5.5 0 0 1 3.395 6V5a1.5 1.5 0 0 0-1.49 1.334zM5 4v-.5H4V4zm5-.5V4h1v-.5zM7.5 1A2.5 2.5 0 0 1 10 3.5h1A3.5 3.5 0 0 0 7.5 0zM5 3.5A2.5 2.5 0 0 1 7.5 1V0A3.5 3.5 0 0 0 4 3.5z" fill="#6f2987"/></svg>
                            </div>',
                'name' => 'Total Inhouse Products',
                'count' => $data['total_inhouse_products'],
                'class' => 'col-lg-4'
            ],
            [
                'icon' =>   '<div class="statistics_icon">
                                <svg fill="#6f2987" width="30" height="30" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M10 14h4a1 1 0 0 0 0-2h-4a1 1 0 0 0 0 2m9-11H5a3 3 0 0 0-3 3v3a1 1 0 0 0 1 1h1v8a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-8h1a1 1 0 0 0 1-1V6a3 3 0 0 0-3-3m-1 15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-8h12Zm2-10H4V6a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1Z"/></svg>
                            </div>',
                'name' => 'Total Categories',
                'count' => $data['total_categories'],
                'class' => 'col-lg-4'
            ],
            [
                'icon' =>   '<div class="statistics_icon">
                                <svg fill="#6f2987" width="30" height="30" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M10 14h4a1 1 0 0 0 0-2h-4a1 1 0 0 0 0 2m9-11H5a3 3 0 0 0-3 3v3a1 1 0 0 0 1 1h1v8a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-8h1a1 1 0 0 0 1-1V6a3 3 0 0 0-3-3m-1 15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-8h12Zm2-10H4V6a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1Z"/></svg>
                            </div>',
                'name' => 'Total Brands',
                'count' => $data['total_brands'],
                'class' => 'col-lg-4'
            ],
        ];


        return view('backend.dashboard', $data);
    }
}
