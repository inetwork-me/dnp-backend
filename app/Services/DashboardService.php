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
        // Product counts by type
        $rawCounts = Product::select('type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $productTypes = ['simple', 'service', 'bundle', 'package', 'session'];
        $productTypeCounts = collect($productTypes)->mapWithKeys(function ($type) use ($rawCounts) {
            return [$type => $rawCounts[$type] ?? 0];
        })->toArray();

        // Current month stats
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Orders analytics
        $currentOrders = DB::table('orders')->where('created_at', '>=', $currentMonth)->count();
        $previousOrders = DB::table('orders')->whereBetween('created_at', [$previousMonth, $currentMonth])->count();
        $ordersGrowth = $this->calculateGrowthPercentage($previousOrders, $currentOrders);

        // Revenue analytics
        $currentRevenue = DB::table('orders')->where('created_at', '>=', $currentMonth)->sum('total_amount');
        $previousRevenue = DB::table('orders')->whereBetween('created_at', [$previousMonth, $currentMonth])->sum('total_amount');
        $revenueGrowth = $this->calculateGrowthPercentage($previousRevenue, $currentRevenue);

        // Products sold analytics
        $currentProductsSold = OrderItem::whereHas('order', function($query) use ($currentMonth) {
            $query->where('created_at', '>=', $currentMonth);
        })->sum('quantity');
        $previousProductsSold = OrderItem::whereHas('order', function($query) use ($previousMonth, $currentMonth) {
            $query->whereBetween('created_at', [$previousMonth, $currentMonth]);
        })->sum('quantity');
        $productsSoldGrowth = $this->calculateGrowthPercentage($previousProductsSold, $currentProductsSold);

        // User growth analytics
        $currentUsers = User::where('created_at', '>=', $currentMonth)->count();
        $previousUsers = User::whereBetween('created_at', [$previousMonth, $currentMonth])->count();
        $usersGrowth = $this->calculateGrowthPercentage($previousUsers, $currentUsers);

        // Ecommerce specific metrics
        $averageOrderValue = DB::table('orders')->count() > 0 ? DB::table('orders')->avg('total_amount') : 0;
        $pendingOrders = DB::table('orders')->where('status', 'pending')->count();
        $completedOrders = DB::table('orders')->where('status', 'completed')->count();
        $lowStockProducts = Product::where('current_stock', '<', 10)->count();
        $topSellingProducts = $this->getTopSellingProducts();
        $recentOrders = $this->getRecentOrders();
        
        // Reviews and ratings
        $totalReviews = DB::table('reviews')->count();
        $averageRating = DB::table('reviews')->avg('rating') ?: 0;

        return [
            // Core metrics with real growth percentages
            'total_orders' => DB::table('orders')->count(),
            'products_sold' => OrderItem::sum('quantity'),
            'revenue' => DB::table('orders')->sum('total_amount'),
            'revenue_currency' => 'EGP',
            'total_users' => User::count(),
            
            // Product analytics
            'product_types_count' => $productTypeCounts,
            'product_categories_count' => ProductCategory::count(),
            'total_coupons' => Coupon::count(),
            'low_stock_products' => $lowStockProducts,
            
            // Order analytics
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'average_order_value' => round($averageOrderValue, 2),
            
            // Customer analytics
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 1),
            
            // Real growth percentages
            'change_percentages' => [
                'total_orders' => $ordersGrowth,
                'products_sold' => $productsSoldGrowth,
                'revenue' => $revenueGrowth,
                'total_users' => $usersGrowth,
                'low_stock_products' => 0, // Static for now
                'pending_orders' => 0, // Static for now  
                'average_order_value' => 0, // Could calculate if needed
                'total_reviews' => 0, // Could calculate if needed
            ],
            
            // Additional data for charts/tables
            'top_selling_products' => $topSellingProducts,
            'recent_orders' => $recentOrders,
            'sales_chart' => $this->getSalesTrend(),
        ];
    }

    private function calculateGrowthPercentage($previous, $current): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function getTopSellingProducts(): array
    {
        return Product::select('products.id', 'products.name')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getRecentOrders(): array
    {
        return DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.id', 'orders.user_id', 'orders.total_amount', 'orders.status', 'orders.created_at', 'users.name as user_name')
            ->orderBy('orders.created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'user_id' => $order->user_id,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                    'user' => $order->user_name ? ['name' => $order->user_name] : null
                ];
            })
            ->toArray();
    }

    private function getSalesTrend(): array
    {
        // Use DB facade to avoid model's withCount
        $monthlyData = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('revenue', 'month');

        $labels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = (float) ($monthlyData[$i] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}
