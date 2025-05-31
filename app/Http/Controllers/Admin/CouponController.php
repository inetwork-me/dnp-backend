<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(20);
        return view('backend.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('backend.coupons.create');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'code'                       => 'required|unique:coupons,code',
            'type'                       => 'required|in:percent,fixed',
            'value'                      => 'required|numeric|min:0',
            'usage_limit_per_customer'   => 'required|integer|min:1',
            'usage_limit_global'         => 'nullable|integer|min:1',
            'starts_at'                  => 'nullable|date',
            'ends_at'                    => 'nullable|date|after_or_equal:starts_at',
            'active'                     => 'boolean',
        ]);

        Coupon::create($data);
        return redirect()->route('coupons.index')
            ->with('success', 'Coupon created.');
    }

    public function show(Coupon $coupon)
    {
        return view('backend.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        return view('backend.coupons.update', compact('coupon'));
    }

    public function update(Request $req, Coupon $coupon)
    {
        $data = $req->validate([
            'code'                       => 'required|unique:coupons,code,' . $coupon->id,
            'type'                       => 'required|in:percent,fixed',
            'value'                      => 'required|numeric|min:0',
            'usage_limit_per_customer'   => 'required|integer|min:1',
            'usage_limit_global'         => 'nullable|integer|min:1',
            'starts_at'                  => 'nullable|date',
            'ends_at'                    => 'nullable|date|after_or_equal:starts_at',
            'active'                     => 'boolean',
        ]);

        $coupon->update($data);
        return redirect()->route('backend.coupons.index')
            ->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }
}
