<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LoyaltyPointsTransaction;
use App\Models\LoyaltySetting;
use App\Models\Voucher;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApiLoyaltyController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Get customer loyalty summary
     * GET /api/v2/loyalty/summary
     */
    public function summary(Request $request)
    {
        $customer = $request->user()->getOrCreateCustomer();

        return response()->json([
            'customer_id' => $customer->id,
            'total_loyalty_points' => $customer->total_loyalty_points,
            'available_loyalty_points' => $customer->available_loyalty_points, // This calls the computed attribute
            'used_loyalty_points' => $customer->used_loyalty_points,
            'membership_tier' => $customer->membership_tier,
            'referral_code' => $customer->referral_code,
            'points_to_next_tier' => $this->getPointsToNextTier($customer),
        ]);
    }

    /**
     * Get loyalty points transaction history
     * GET /api/v2/loyalty/transactions
     */
    public function transactions(Request $request)
    {
        $customer = $request->user()->getOrCreateCustomer();
        
        $transactions = $customer->loyaltyTransactions()
            ->with(['order', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($transactions);
    }

    /**
     * Convert points to voucher
     * POST /api/v2/loyalty/convert-to-voucher
     */
    public function convertToVoucher(Request $request)
    {
        $data = $request->validate([
            'points' => 'required|integer|min:1'
        ]);

        $customer = $request->user()->getOrCreateCustomer();

        try {
            $voucher = $this->loyaltyService->createVoucherFromPoints($customer, $data['points']);
            
            return response()->json([
                'message' => 'Voucher created successfully',
                'voucher' => $voucher
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get loyalty system settings (for display)
     * GET /api/v2/loyalty/settings
     */
    public function settings()
    {
        $settings = [
            // General Settings
            'loyalty_enabled' => LoyaltySetting::get('loyalty_enabled', true),
            'default_product_points' => LoyaltySetting::get('default_product_points', 10),
            'minimum_order_amount' => LoyaltySetting::get('minimum_order_amount', 0),
            
            // Point Conversion
            'points_to_currency_rate' => LoyaltySetting::get('points_to_currency_rate', 100),
            'default_currency' => LoyaltySetting::get('default_currency', 'USD'),
            
            // Expiry Settings
            'points_expire' => LoyaltySetting::get('points_expire', false),
            'points_expiry_days' => LoyaltySetting::get('points_expiry_days', 365),
            'voucher_expiry_days' => LoyaltySetting::get('voucher_expiry_days', 90),
            
            // Membership Tiers
            'bronze_tier_points' => LoyaltySetting::get('bronze_tier_points', 0),
            'silver_tier_points' => LoyaltySetting::get('silver_tier_points', 1000),
            'gold_tier_points' => LoyaltySetting::get('gold_tier_points', 5000),
            'platinum_tier_points' => LoyaltySetting::get('platinum_tier_points', 10000),
            
            // Bonus Points
            'signup_bonus_points' => LoyaltySetting::get('signup_bonus_points', 100),
            'referral_points_referrer' => LoyaltySetting::get('referral_points_referrer', 500),
            'referral_points_referred' => LoyaltySetting::get('referral_points_referred', 250),
            
            // Return Policy
            'deduct_points_on_return' => LoyaltySetting::get('deduct_points_on_return', true)
        ];

        return response()->json($settings);
    }

    /**
     * Admin: Manually add/remove points
     * POST /api/v2/loyalty/manual-adjustment
     */
    public function manualAdjustment(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'points' => 'required|integer',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:manual_add,manual_deduct'
        ]);

        $customer = Customer::find($data['customer_id']);

        try {
            if ($data['points'] > 0) {
                $customer->addLoyaltyPoints(
                    abs($data['points']),
                    $data['type'],
                    null,
                    null,
                    $data['description'] ?? 'Manual adjustment by admin'
                );
            } else {
                $customer->deductLoyaltyPoints(
                    abs($data['points']),
                    $data['type'],
                    null,
                    $data['description'] ?? 'Manual adjustment by admin'
                );
            }

            return response()->json([
                'message' => 'Points adjusted successfully',
                'customer' => $customer->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Admin: Get all customers with loyalty data
     * GET /api/v2/loyalty/customers
     */
    public function customers(Request $request)
    {
        $customers = Customer::with(['user', 'loyaltyTransactions'])
            ->orderBy('total_loyalty_points', 'desc')
            ->paginate(20);

        return response()->json($customers);
    }

    /**
     * Admin: Update loyalty settings
     * POST /api/v2/loyalty/admin/settings
     */
    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
            'settings.*.type' => 'required|in:string,integer,boolean,decimal'
        ]);

        foreach ($data['settings'] as $setting) {
            LoyaltySetting::set(
                $setting['key'],
                $setting['value'],
                $setting['type'],
                null,
                $setting['group'] ?? 'general'
            );
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }

    private function getPointsToNextTier(Customer $customer)
    {
        $currentPoints = $customer->total_loyalty_points; // Use total earned points for tier progression
        $tier = $customer->membership_tier;

        $tiers = [
            'bronze' => LoyaltySetting::get('silver_tier_points', 1000),
            'silver' => LoyaltySetting::get('gold_tier_points', 5000),
            'gold' => LoyaltySetting::get('platinum_tier_points', 10000),
            'platinum' => null
        ];

        $nextTierPoints = $tiers[$tier] ?? null;
        
        if ($nextTierPoints === null) {
            return null; // Already at highest tier
        }

        return max(0, $nextTierPoints - $currentPoints);
    }

    /**
     * Get loyalty dashboard statistics
     * GET /api/v2/loyalty/dashboard-stats
     */
    public function dashboardStats(Request $request)
    {
        // Current month and previous month for comparison
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        // Total customers with loyalty points
        $totalCustomers = Customer::where('total_loyalty_points', '>', 0)->count();
        $previousCustomers = Customer::where('total_loyalty_points', '>', 0)
            ->where('created_at', '<', $currentMonth)
            ->count();
        $customerChange = $previousCustomers > 0 ? (($totalCustomers - $previousCustomers) / $previousCustomers * 100) : 0;

        // Active vouchers
        $activeVouchers = Voucher::where('status', 'active')
            ->where('expires_at', '>', Carbon::now())
            ->count();
        $previousActiveVouchers = Voucher::where('status', 'active')
            ->where('created_at', '<', $currentMonth)
            ->count();
        $voucherChange = $previousActiveVouchers > 0 ? (($activeVouchers - $previousActiveVouchers) / $previousActiveVouchers * 100) : 0;

        // Total points issued
        $totalPointsIssued = LoyaltyPointsTransaction::where('type', 'earned')->sum('points');
        $previousPointsIssued = LoyaltyPointsTransaction::where('type', 'earned')
            ->where('created_at', '<', $currentMonth)
            ->sum('points');
        $pointsChange = $previousPointsIssued > 0 ? (($totalPointsIssued - $previousPointsIssued) / $previousPointsIssued * 100) : 0;

        // Total voucher value
        $totalVoucherValue = Voucher::where('status', 'active')->sum('value');
        $previousVoucherValue = Voucher::where('status', 'active')
            ->where('created_at', '<', $currentMonth)
            ->sum('value');
        $valueChange = $previousVoucherValue > 0 ? (($totalVoucherValue - $previousVoucherValue) / $previousVoucherValue * 100) : 0;

        return response()->json([
            'total_customers' => [
                'value' => $totalCustomers,
                'change' => round($customerChange, 1)
            ],
            'active_vouchers' => [
                'value' => $activeVouchers,
                'change' => round($voucherChange, 1)
            ],
            'points_issued' => [
                'value' => $totalPointsIssued,
                'change' => round($pointsChange, 1)
            ],
            'voucher_value' => [
                'value' => $totalVoucherValue,
                'change' => round($valueChange, 1)
            ]
        ]);
    }
}
