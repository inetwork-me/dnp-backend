<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Consultation;
use App\Models\Appointment;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getOverview(): array
    {

        $rawCounts = Product::select('type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $productTypes = ['simple', 'service', 'bundle', 'package', 'session'];

        $productTypeCounts = collect($productTypes)->mapWithKeys(function ($type) use ($rawCounts) {
            return [$type => $rawCounts[$type] ?? 0];
        })->toArray();



        return [
            'total_orders' => Order::count(),
            'products_sold' => OrderItem::sum('quantity'),
            'revenue' => Order::sum('total_amount'),
            'revenue_currency' => 'EGP',
            // ✅ Products by type
            'product_types_count' => $productTypeCounts,
            'product_categories_count' => ProductCategory::count(),
            'total_users' => User::count(),
            'total_coupons' => Coupon::count(),
            'change_percentages' => [
                'total_orders' => 3.5,
                'products_sold' => 2.0,
                'revenue' => 3.5,
                'engagement_rate' => -0.5,
                'subscriptions' => -1.4,
                'consultations' => 3.5,
                'appointments' => 2.0,
                'total_users' => 4.0
            ],
            // 'sales_chart' => $this->getSalesTrend(),
        ];
    }

    // protected function getSalesTrend(): array
    // {
    //     $monthlyData = Order::selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
    //         ->groupByRaw('MONTH(created_at)')
    //         ->pluck('revenue', 'month');

    //     $labels = [
    //         'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    //         'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    //     ];

    //     $data = [];
    //     for ($i = 1; $i <= 12; $i++) {
    //         $data[] = (float) ($monthlyData[$i] ?? 0);
    //     }

    //     return [
    //         'labels' => $labels,
    //         'data' => $data
    //     ];
    // }
}
