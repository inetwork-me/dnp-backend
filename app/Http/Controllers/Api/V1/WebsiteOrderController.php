<?php

// app/Http/Controllers/WebsiteOrderController.php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Hash;

class WebsiteOrderController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }
    // GET /api/orders
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.product', 'latestShipment'])
            ->get();

        // Ensure total_amount is calculated for orders that might be missing it
        foreach ($orders as $order) {
            if (!$order->total_amount || $order->total_amount == 0) {
                $subtotal = $order->subtotal ?: $order->items->sum(fn($item) => $item->quantity * $item->unit_price);
                $discount = $order->discount ?: 0;
                $shipping = $order->shipping_cost ?: 0;
                $tax = $order->tax ?: 0;

                $order->total_amount = max(0, $subtotal - $discount + $shipping + $tax);
                $order->save();
            }
        }

        return $orders;
    }

    // GET /api/orders/{order}
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return $order->load('items.product');
    }

    public function store(Request $request)
    {
        $shippingEnabled = is_shipping_enabled();

        $validationRules = [
            'cart_id'           => 'nullable|exists:carts,id',
            'cart_items'        => 'required_without:cart_id|array',
            'cart_items.*.product_id' => 'required_with:cart_items|exists:products,id',
            'cart_items.*.quantity' => 'required_with:cart_items|integer|min:1',
            'cart_items.*.options' => 'nullable|array',
            'cart_items.*.branch_id' => 'nullable|exists:branches,id',
            'billing_address'   => 'array|nullable',
            'payment_method'    => 'string|nullable',
            'coupon_code'       => 'string|nullable',
            'voucher_code'      => 'string|nullable',
            'currency'          => 'string|nullable|in:EGP,USD,SAR,AED',
            'currency_rate'     => 'numeric|nullable|min:0',
        ];

        // Only require guest fields if user is not authenticated
        if (!$request->user()) {
            $validationRules['guest_email'] = 'required|email';
            $validationRules['guest_name'] = 'required|string';
            $validationRules['guest_phone'] = 'nullable|string';
        }

        // Add shipping validation only if shipping is enabled
        if ($shippingEnabled) {
            $validationRules['shipping_address'] = 'nullable|array';
            $validationRules['shipping_method_id'] = 'nullable';
            $validationRules['shipping_cost'] = 'nullable|numeric|min:0';
            $validationRules['shipping_quote_data'] = 'nullable|array';
        }

        $data = $request->validate($validationRules);

        // 1) load or create cart + applied coupon with ownership verification
        // Use user_id from request if provided, otherwise check auth
        $userId = $request->input('user_id') ?? optional($request->user())->id;
        $guestToken = $request->header('X-Guest-Token');

        // If cart_id provided, load existing cart
        if (!empty($data['cart_id'])) {
            $cart = Cart::with(['items.product', 'coupon'])
                ->findOrFail($data['cart_id']);

            // Verify cart ownership
            if ($userId) {
                // Authenticated user - verify user_id matches
                if ($cart->user_id !== $userId) {
                    abort(403, 'Unauthorized to access this cart');
                }
            } else if ($guestToken) {
                // Guest user - verify guest_token matches or cart has no token (legacy cart)
                if ($cart->guest_token && $cart->guest_token !== $guestToken) {
                    // Cart belongs to a different guest
                    abort(403, 'Unauthorized to access this cart');
                }
                // If cart has no guest_token (legacy), allow access and update it
                if (!$cart->guest_token) {
                    $cart->update(['guest_token' => $guestToken]);
                }
            } else {
                abort(400, 'Authentication or guest token required');
            }
        } else {
            // Create cart from cart_items
            if (empty($data['cart_items'])) {
                abort(400, 'Either cart_id or cart_items must be provided');
            }

            // Create new cart
            $cart = Cart::create([
                'user_id' => $userId,
                'guest_token' => !$userId ? $guestToken : null,
                'status' => 'open',
            ]);

            // Add items to cart
            foreach ($data['cart_items'] as $item) {
                $product = Product::with('branches')->findOrFail($item['product_id']);

                // Validate branch selection for service products
                if ($product->requires_branch_selection) {
                    if (empty($item['branch_id'])) {
                        abort(422, 'Branch selection is required for ' . $product->getTranslation('name'));
                    }

                    // Verify branch is available for this product
                    $branchAvailable = $product->branches()->where('branches.id', $item['branch_id'])->exists();
                    if (!$branchAvailable) {
                        abort(422, 'Selected branch is not available for ' . $product->getTranslation('name'));
                    }
                }

                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->unit_price,
                    'options' => $item['options'] ?? [],
                    'branch_id' => $item['branch_id'] ?? null,
                ]);
            }

            // Load relationships
            $cart->load(['items.product', 'coupon']);
        }

        abort_if($cart->items->isEmpty(), 400, 'Cart is empty.');

        // 2) find or create user FIRST (before coupon validation)
        $user = $request->user();
        $isGuestUser = false;
        $guestPassword = null;

        if (!$user) {
            // Guest checkout - check if email already exists
            $existingUser = \App\Models\User::where('email', $data['guest_email'])->first();

            if ($existingUser) {
                // Email already registered - reject checkout
                abort(422, 'An account with this email already exists. Please login or use a different email address.');
            }

            // Create new user for guest
            $guestPassword = Str::random(12);
            $user = \App\Models\User::create([
                'email' => $data['guest_email'],
                'password' => Hash::make($guestPassword),
                'name'     => $data['guest_name'],
                'phone'    => $data['guest_phone'] ?? null,
            ]);

            // Assign client role to guest user
            $clientRole = \Spatie\Permission\Models\Role::where('name', 'client')->first();
            if ($clientRole && !$user->hasRole('client')) {
                $user->assignRole('client');
            }

            $cart->user()->associate($user);
            $cart->save();
            $isGuestUser = true;
        } else {
            // For logged-in users, ensure cart is associated with the authenticated user
            if ($cart->user_id !== $user->id) {
                $cart->user()->associate($user);
                $cart->save();
            }
        }

        // 2.5) Apply coupon if provided and not already applied (AFTER user creation)
        if (!empty($data['coupon_code']) && (!$cart->coupon || $cart->coupon->code !== $data['coupon_code'])) {
            $coupon = Coupon::where('code', $data['coupon_code'])
                ->where('active', true)
                ->first();

            if (!$coupon) {
                abort(400, 'Invalid coupon code');
            }

            // Check if coupon is valid using comprehensive validation with the actual user
            if (!$coupon->isValidForUser($user)) {
                if ($coupon->ends_at && now()->isAfter($coupon->ends_at)) {
                    abort(400, 'Coupon has expired');
                } elseif ($coupon->starts_at && now()->isBefore($coupon->starts_at)) {
                    abort(400, 'Coupon is not yet active');
                } elseif ($coupon->usage_limit_global && $coupon->redemptions()->count() >= $coupon->usage_limit_global) {
                    abort(400, 'Coupon usage limit reached');
                } elseif ($coupon->usage_limit_per_customer && $coupon->redemptions()->where('user_id', $user->id)->count() >= $coupon->usage_limit_per_customer) {
                    abort(400, 'You have already used this coupon the maximum number of times');
                } else {
                    abort(400, 'Coupon is not valid');
                }
            }

            // Apply coupon to cart
            $cart->update(['coupon_id' => $coupon->id]);
            $cart->load('coupon'); // Reload with coupon relationship
        }

        // 2.6) Validate voucher if provided
        $appliedVoucher = null;
        if (!empty($data['voucher_code'])) {
            $customer = $user->getOrCreateCustomer();
            $voucher = \App\Models\Voucher::where('code', $data['voucher_code'])
                ->where(function ($query) use ($customer) {
                    $query->where('customer_id', $customer->id) // Personal voucher
                          ->orWhereNull('customer_id'); // General/promotional voucher
                })
                ->first();

            if (!$voucher) {
                abort(400, 'Invalid voucher code');
            }

            if (!$voucher->isUsable()) {
                abort(400, $voucher->isExpired() ? 'Voucher has expired' : 'Voucher is not active');
            }

            $appliedVoucher = $voucher;
        }

        // 3) snapshot cart → order
        $order = DB::transaction(function () use ($cart, $data, $user, $shippingEnabled, $appliedVoucher) {
            // a) compute amounts using discounted prices (same as cart display)
            $subtotal = $cart->items->sum(fn ($i) => $i->quantity * home_discounted_base_price($i->product, false));
            $discount = $cart->coupon
                ? $cart->coupon->calculateDiscount($subtotal)
                : 0;

            // Apply voucher discount
            $voucherDiscount = $appliedVoucher ? $appliedVoucher->value : 0;

            // Only add shipping cost if shipping is enabled
            $shippingCost = 0;
            if ($shippingEnabled && isset($data['shipping_cost'])) {
                $shippingCost = $data['shipping_cost'];
            }

            $total = max(0, $subtotal - $discount - $voucherDiscount + $shippingCost);

            // Handle shipping method ID for live rates only if shipping is enabled
            $shippingMethodId = null;
            if ($shippingEnabled && isset($data['shipping_method_id'])) {
                $methodId = $data['shipping_method_id'];
                // Check if it's a live rate (starts with 'live_')
                if (is_string($methodId) && str_starts_with($methodId, 'live_')) {
                    // For live rates, set shipping_method_id to null
                    // The full shipping info is stored in shipping_quote_data
                    $shippingMethodId = null;
                } else {
                    // For fixed shipping methods, use the method ID
                    $shippingMethodId = is_numeric($methodId) ? (int)$methodId : null;
                }
            }

            // b) create the order — **note** the use of 'total_amount' here
            $order = Order::create([
                'user_id'          => $user->id,
                'cart_id'          => $cart->id,
                'order_number'     => now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'status'           => 'pending',

                // pricing fields — adjust names to match your table:
                'subtotal'         => round($subtotal, 2),
                'discount'         => round($discount + $voucherDiscount, 2), // Combined coupon + voucher discount
                'total_amount'     => round($total, 2),
                'coupon_id'        => $cart->coupon_id,

                // currency fields (for display purposes)
                'currency'         => $data['currency'] ?? 'EGP',
                'currency_rate'    => $data['currency_rate'] ?? 1,

                // shipping fields (only if shipping enabled)
                'shipping_method_id'    => $shippingMethodId,
                'shipping_cost'         => round($shippingCost, 2),
                'shipping_quote_data'   => $shippingEnabled ? ($data['shipping_quote_data'] ?? null) : null,

                'shipping_address' => $shippingEnabled ? ($data['shipping_address'] ?? null) : null,
                'billing_address'  => $data['billing_address'] ?? ($shippingEnabled ? ($data['shipping_address'] ?? null) : null),
                'payment_method'   => $data['payment_method'] ?? null,
                'payment_status'   => 'unpaid',
            ]);

            // c) copy each cart item and update product stock
            foreach ($cart->items as $ci) {
                $order->items()->create([
                    'product_id' => $ci->product_id,
                    'quantity'   => $ci->quantity,
                    'unit_price' => $ci->unit_price,
                    'line_total' => $ci->quantity * $ci->unit_price,
                    'options'    => $ci->options,
                    'branch_id'  => $ci->branch_id,
                ]);

                // Deduct stock from product
                $product = \App\Models\Product::find($ci->product_id);
                if ($product) {
                    $product->decrement('current_stock', $ci->quantity);
                }
            }

            // d) record coupon redemption + bump global counter
            if ($cart->coupon) {
                DB::transaction(function () use ($cart, $order, $discount, $user) {
                    // Final validation check to prevent race conditions
                    if (!$cart->coupon->isValidForUser($user)) {
                        throw new \Exception('Coupon is no longer valid');
                    }

                    $cart->coupon->redemptions()->create([
                        'user_id'  => $user->id,
                        'cart_id'  => $cart->id,
                        'order_id' => $order->id,
                        'discount' => $discount,
                    ]);
                    $cart->coupon->increment('times_used');
                });
            }

            // e) mark voucher as used if applied
            if ($appliedVoucher) {
                $appliedVoucher->markAsUsed($order);
            }

            // f) close out the cart
            $cart->update(['status' => 'converted']);

            return $order;
        });

        // 4) Handle guest user account setup
        if ($isGuestUser && $guestPassword) {
            try {
                // Create customer profile for guest user
                $customer = $user->getOrCreateCustomer();

                // Update customer with billing address information
                $customerUpdateData = [];

                if (isset($data['billing_address'])) {
                    $customerUpdateData = [
                        'billing_address' => $data['billing_address']['line1'] ?? null,
                        'billing_city' => $data['billing_address']['city'] ?? null,
                        'billing_state' => $data['billing_address']['state'] ?? null,
                        'billing_country' => $data['billing_address']['country'] ?? null,
                        'billing_postal_code' => $data['billing_address']['postal_code'] ?? null,
                    ];
                }

                // Update customer with shipping information if available
                if (isset($data['shipping_address'])) {
                    $customerUpdateData = array_merge($customerUpdateData, [
                        'shipping_address' => $data['shipping_address']['line1'] ?? null,
                        'shipping_city' => $data['shipping_address']['city'] ?? null,
                        'shipping_state' => $data['shipping_address']['state'] ?? null,
                        'shipping_country' => $data['shipping_address']['country'] ?? null,
                        'shipping_postal_code' => $data['shipping_address']['postal_code'] ?? null,
                    ]);
                }

                if (!empty($customerUpdateData)) {
                    $customer->update($customerUpdateData);
                }

                // Send welcome email with login credentials if enabled
                $welcomeEmailEnabled = \App\Models\BusinessSetting::where('type', 'customer_welcome_email_enabled')->first();
                if ($welcomeEmailEnabled && $welcomeEmailEnabled->value === '1') {
                    $user->notify(new \App\Notifications\GuestUserWelcomeNotification($user, $guestPassword, $order));
                    \Log::info('Welcome email sent to guest user: ' . $user->email);
                } else {
                    \Log::info('Customer welcome emails are disabled, skipping for user: ' . $user->email);
                }

            } catch (\Exception $e) {
                // Log error but don't fail the order
                \Log::error('Guest user setup failed for order ' . $order->id . ': ' . $e->getMessage());
            }
        }

        // 5) Send admin notification for new order
        try {
            // Check if new order emails are enabled
            $newOrderEmailEnabled = \App\Models\BusinessSetting::where('type', 'new_order_email_enabled')->first();
            if (!$newOrderEmailEnabled || $newOrderEmailEnabled->value !== '1') {
                \Log::info('New order email notifications are disabled, skipping for order ' . $order->order_number);
            } else {
                // Get admin emails from settings
                $adminEmailsSetting = \App\Models\BusinessSetting::where('type', 'admin_notification_emails')->first();
                $adminEmails = $adminEmailsSetting ? json_decode($adminEmailsSetting->value, true) : [];

                // Fallback to env if no admin emails configured
                if (empty($adminEmails)) {
                    $adminEmails = [env('ADMIN_NOTIFICATION_EMAIL', config('mail.from.address'))];
                }

                // Send notification to all admin emails
                foreach ($adminEmails as $adminEmail) {
                    if (filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                        \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
                            ->notify(new \App\Notifications\NewOrderAdminNotification($order));
                    }
                }

                \Log::info('Admin notifications sent for order ' . $order->order_number . ' to ' . count($adminEmails) . ' admin(s)');
            }
        } catch (\Exception $e) {
            // Log error but don't fail the order
            \Log::error('Admin notification failed for order ' . $order->id . ': ' . $e->getMessage());
        }

        // 6) Loyalty points will be processed when order status changes to 'completed'
        // No longer processing loyalty points immediately on order creation

        // 6) return with items & coupon
        return response()->json(
            $order->load('items.product', 'coupon'),
            201
        );
    }

    // PUT /api/v1/orders/{order}/payment-status
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_status' => 'required|in:paid,unpaid,failed,refunded',
            'payment_details' => 'nullable|array',
        ]);

        // Update payment status
        $order->update([
            'payment_status' => $data['payment_status'],
        ]);

        // If payment is confirmed, update order status to processing
        if ($data['payment_status'] === 'paid' && $order->status === 'pending') {
            $order->update(['status' => 'processing']);
        }

        return response()->json([
            'message' => 'Payment status updated successfully',
            'order' => $order->load('items.product', 'coupon'),
        ]);
    }
}
