<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\CouponCollection;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApiCouponController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);
        $coupons = Coupon::withCount('redemptions')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Add usage statistics to each coupon
        $coupons->getCollection()->transform(function ($coupon) {
            $coupon->usage_count = $coupon->redemptions_count;
            $coupon->remaining_global = $coupon->usage_limit_global ? max(0, $coupon->usage_limit_global - $coupon->usage_count) : null;
            return $coupon;
        });

        return new CouponCollection($coupons);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'                     => 'required|unique:coupons,code',
            'type'                     => ['required', Rule::in(['percent', 'fixed'])],
            'value'                    => 'required|numeric|min:0',
            'usage_limit_per_customer' => 'required|integer|min:1',
            'usage_limit_global'       => 'nullable|integer|min:1',
            'starts_at'                => 'nullable|date',
            'ends_at'                  => 'nullable|date|after_or_equal:starts_at',
            'active'                   => 'boolean',
        ]);

        $coupon = Coupon::create($data);
        return response()->json($coupon, 201);
    }

    public function show(Coupon $coupon)
    {
        // Load the coupon with usage statistics
        $coupon->load('redemptions');
        $coupon->usage_count = $coupon->redemptions()->count();
        $coupon->remaining_global = $coupon->usage_limit_global ? max(0, $coupon->usage_limit_global - $coupon->usage_count) : null;

        return response()->json($coupon);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'code'                     => ['required', Rule::unique('coupons', 'code')->ignore($coupon->id)],
            'type'                     => ['required', Rule::in(['percent', 'fixed'])],
            'value'                    => 'required|numeric|min:0',
            'usage_limit_per_customer' => 'required|integer|min:1',
            'usage_limit_global'       => 'nullable|integer|min:1',
            'starts_at'                => 'nullable|date',
            'ends_at'                  => 'nullable|date|after_or_equal:starts_at',
            'active'                   => 'boolean',
        ]);

        $coupon->update($data);
        return response()->json($coupon);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }

    public function apply(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string',
            'cart_total' => 'required|numeric|min:0'
        ]);

        $coupon = Coupon::where('code', $data['code'])
            ->where('active', true)
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ], 400);
        }

        // Use the model's comprehensive validation method
        $user = $request->user(); // Get current authenticated user (if any)
        if (!$coupon->isValidForUser($user)) {
            // Determine specific error message
            if ($coupon->ends_at && now()->isAfter($coupon->ends_at)) {
                $message = 'Coupon has expired';
            } elseif ($coupon->starts_at && now()->isBefore($coupon->starts_at)) {
                $message = 'Coupon is not yet active';
            } elseif ($coupon->usage_limit_global && $coupon->redemptions()->count() >= $coupon->usage_limit_global) {
                $message = 'Coupon usage limit reached';
            } elseif ($coupon->usage_limit_per_customer && $user && $coupon->redemptions()->where('user_id', $user->id)->count() >= $coupon->usage_limit_per_customer) {
                $message = 'You have already used this coupon the maximum number of times';
            } else {
                $message = 'Coupon is not valid';
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        }

        // Calculate discount amount using model method
        $discountAmount = $coupon->calculateDiscount($data['cart_total']);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully',
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount_type' => $coupon->type,
                'discount_value' => $coupon->value,
                'calculated_discount' => $discountAmount
            ]
        ]);
    }

    /**
     * Get coupon usage statistics
     */
    public function usage(Coupon $coupon)
    {
        $coupon->load(['redemptions' => function ($query) {
            $query->with(['user:id,name,email', 'order:id,order_number,total_amount,created_at'])
                  ->orderBy('created_at', 'desc');
        }]);

        $usageStats = [
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'total_usage' => $coupon->redemptions()->count(),
            'usage_limit_global' => $coupon->usage_limit_global,
            'usage_limit_per_customer' => $coupon->usage_limit_per_customer,
            'remaining_global' => $coupon->usage_limit_global ? max(0, $coupon->usage_limit_global - $coupon->redemptions()->count()) : null,
            'unique_customers' => $coupon->redemptions()->distinct('user_id')->count('user_id'),
            'total_discount_given' => $coupon->redemptions()->sum('discount'),
            'recent_redemptions' => $coupon->redemptions->take(10),
        ];

        return response()->json($usageStats);
    }

    /**
     * Get detailed redemption logs for a coupon
     */
    public function redemptions(Request $request, Coupon $coupon)
    {
        $perPage = $request->query('per_page', 20);

        $redemptions = $coupon->redemptions()
            ->with(['user:id,name,email', 'order:id,order_number,total_amount,created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($redemptions);
    }

    /**
     * Get default currency settings
     */
    public function getDefaultCurrency()
    {
        // Get system default currency
        $defaultCurrency = get_system_default_currency();

        return response()->json([
            'code' => $defaultCurrency->code,
            'symbol' => $defaultCurrency->symbol,
            'name' => $defaultCurrency->name,
        ]);
    }
}
