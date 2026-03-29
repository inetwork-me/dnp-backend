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
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate($perPage);
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
}
