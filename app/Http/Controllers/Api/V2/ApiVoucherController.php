<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class ApiVoucherController extends Controller
{
    /**
     * Get customer's vouchers
     * GET /api/v2/vouchers
     */
    public function index(Request $request)
    {
        $customer = $request->user()->getOrCreateCustomer();
        
        $vouchers = $customer->vouchers()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($vouchers);
    }

    /**
     * Get active vouchers only
     * GET /api/v2/vouchers/active
     */
    public function active(Request $request)
    {
        $customer = $request->user()->getOrCreateCustomer();
        
        $vouchers = $customer->vouchers()
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($vouchers);
    }

    /**
     * Validate a voucher code
     * POST /api/v2/vouchers/validate
     */
    public function validateVoucher(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string'
        ]);

        $customer = $request->user()->getOrCreateCustomer();

        // Look for voucher - either belongs to current customer OR is a general voucher (no customer_id)
        $voucher = Voucher::where('code', $data['code'])
            ->where(function ($query) use ($customer) {
                $query->where('customer_id', $customer->id) // Personal voucher
                      ->orWhereNull('customer_id'); // General/promotional voucher
            })
            ->first();

        if (!$voucher) {
            return response()->json([
                'valid' => false,
                'message' => 'Voucher not found or not valid for this account'
            ], 404);
        }

        if (!$voucher->isUsable()) {
            return response()->json([
                'valid' => false,
                'message' => $voucher->isExpired() ? 'Voucher has expired' : 'Voucher is not active',
                'voucher' => $voucher
            ], 400);
        }

        return response()->json([
            'valid' => true,
            'voucher' => $voucher,
            'message' => 'Voucher is valid and can be used'
        ]);
    }

    /**
     * Admin: Get all vouchers
     * GET /api/v2/vouchers/admin
     */
    public function adminIndex(Request $request)
    {
        $vouchers = Voucher::with(['customer.user'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->customer_id, function ($query, $customerId) {
                return $query->where('customer_id', $customerId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($vouchers);
    }

    /**
     * Admin: Create voucher manually
     * POST /api/v2/vouchers/admin
     */
    public function adminStore(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'value' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|size:3',
            'expires_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string'
        ]);

        $voucher = Voucher::create([
            'customer_id' => $data['customer_id'],
            'value' => $data['value'],
            'currency' => $data['currency'] ?? 'USD',
            'points_used' => 0, // Manual creation
            'expires_at' => $data['expires_at'] ?? null,
            'created_by' => $request->user()->id,
            'notes' => $data['notes'] ?? 'Created manually by admin'
        ]);

        return response()->json([
            'message' => 'Voucher created successfully',
            'voucher' => $voucher->load('customer.user')
        ], 201);
    }

    /**
     * Admin: Update voucher status
     * PUT /api/v2/vouchers/{voucher}/status
     */
    public function updateStatus(Request $request, Voucher $voucher)
    {
        $data = $request->validate([
            'status' => 'required|in:active,cancelled,expired',
            'notes' => 'nullable|string'
        ]);

        $voucher->update([
            'status' => $data['status'],
            'notes' => $data['notes'] ? $voucher->notes . "\n" . $data['notes'] : $voucher->notes
        ]);

        return response()->json([
            'message' => 'Voucher status updated',
            'voucher' => $voucher->fresh()
        ]);
    }

    /**
     * Get voucher usage statistics
     * GET /api/v2/vouchers/stats
     */
    public function stats()
    {
        $stats = [
            'total_vouchers' => Voucher::count(),
            'active_vouchers' => Voucher::active()->count(),
            'used_vouchers' => Voucher::used()->count(),
            'expired_vouchers' => Voucher::expired()->count(),
            'total_value' => [
                'active' => Voucher::active()->sum('value'),
                'used' => Voucher::used()->sum('value'),
                'total' => Voucher::sum('value')
            ],
            'recent_vouchers' => Voucher::with('customer.user')
                ->latest()
                ->limit(5)
                ->get()
        ];

        return response()->json($stats);
    }
}
