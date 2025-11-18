<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\LoyaltySetting;
use App\Models\User;

class LoyaltyService
{
    public function processOrderLoyaltyPoints(Order $order)
    {
        // Check if loyalty is enabled
        if (!LoyaltySetting::get('loyalty_enabled', false)) {
            return;
        }

        // Get or create customer profile
        $user = $order->user;
        $customer = $user->getOrCreateCustomer();

        // Check minimum order amount
        $minimumAmount = LoyaltySetting::get('minimum_order_amount', 0);
        if ($order->total_amount < $minimumAmount) {
            return;
        }

        // Award points per product
        foreach ($order->items as $orderItem) {
            $product = $orderItem->product;
            $pointsPerUnit = $product->getEffectiveLoyaltyPoints();
            
            if ($pointsPerUnit > 0) {
                $totalPoints = $pointsPerUnit * $orderItem->quantity;
                
                // Award the points
                $customer->addLoyaltyPoints(
                    $totalPoints,
                    'earned',
                    $order,
                    $product,
                    "Earned {$totalPoints} points for purchasing {$orderItem->quantity}x {$product->name}"
                );
            }
        }

        // Update membership tier based on new total
        $customer->updateMembershipTier();

        // Award signup bonus if this is first order
        if ($customer->total_orders == 0) {
            $signupBonus = LoyaltySetting::get('signup_bonus_points', 0);
            if ($signupBonus > 0) {
                $customer->addLoyaltyPoints(
                    $signupBonus,
                    'bonus',
                    $order,
                    null,
                    'Welcome bonus for first order'
                );
            }
        }

        // Update customer stats
        $customer->increment('total_orders');
        $customer->increment('lifetime_value', $order->total_amount);
        $customer->average_order_value = $customer->lifetime_value / $customer->total_orders;
        $customer->save();
    }

    public function refundOrderLoyaltyPoints(Order $order)
    {
        // Check if we should deduct points on returns
        if (!LoyaltySetting::get('deduct_points_on_return', true)) {
            return;
        }

        $user = $order->user;
        $customer = $user->getOrCreateCustomer();

        // Find all earned points for this order and deduct them
        $earnedTransactions = $customer->loyaltyTransactions()
            ->where('order_id', $order->id)
            ->where('type', 'earned')
            ->get();

        foreach ($earnedTransactions as $transaction) {
            if ($customer->available_loyalty_points >= abs($transaction->points)) {
                $customer->deductLoyaltyPoints(
                    abs($transaction->points),
                    'refunded',
                    $order,
                    "Points deducted due to order refund/cancellation"
                );
            }
        }

        // Update customer stats
        $customer->decrement('total_orders');
        $customer->decrement('lifetime_value', $order->total_amount);
        
        if ($customer->total_orders > 0) {
            $customer->average_order_value = $customer->lifetime_value / $customer->total_orders;
        } else {
            $customer->average_order_value = 0;
        }
        
        $customer->save();
        $customer->updateMembershipTier();
    }

    public function createVoucherFromPoints(Customer $customer, int $points)
    {
        // Check if customer has enough points
        if ($customer->available_loyalty_points < $points) {
            throw new \Exception('Insufficient loyalty points');
        }

        // Get conversion rate
        $conversionRate = LoyaltySetting::get('points_to_currency_rate', 100);
        $voucherValue = $points / $conversionRate;

        // Get expiry days
        $expiryDays = LoyaltySetting::get('voucher_expiry_days', 90);

        // Create voucher
        return \App\Models\Voucher::createFromPoints($customer, $points, $voucherValue, $expiryDays);
    }

    public function processReferralBonus(User $newUser, User $referrer)
    {
        $referrerCustomer = $referrer->getOrCreateCustomer();
        $newCustomer = $newUser->getOrCreateCustomer();

        // Award points to referrer
        $referrerPoints = LoyaltySetting::get('referral_points_referrer', 100);
        if ($referrerPoints > 0) {
            $referrerCustomer->addLoyaltyPoints(
                $referrerPoints,
                'bonus',
                null,
                null,
                "Referral bonus for referring {$newUser->name}"
            );
        }

        // Award points to new user
        $referredPoints = LoyaltySetting::get('referral_points_referred', 50);
        if ($referredPoints > 0) {
            $newCustomer->addLoyaltyPoints(
                $referredPoints,
                'bonus',
                null,
                null,
                "Welcome bonus for being referred by {$referrer->name}"
            );
        }
    }

    public function expireOldPoints()
    {
        if (!LoyaltySetting::get('points_expire', false)) {
            return;
        }

        $expiryDays = LoyaltySetting::get('points_expiry_days', 365);
        $expiryDate = now()->subDays($expiryDays);

        // Find transactions that should expire
        $expiredTransactions = \App\Models\LoyaltyPointsTransaction::where('type', 'earned')
            ->where('created_at', '<=', $expiryDate)
            ->get();

        foreach ($expiredTransactions as $transaction) {
            $customer = $transaction->customer;
            
            // Only expire if points haven't been used
            if ($customer->available_loyalty_points >= $transaction->points) {
                $customer->deductLoyaltyPoints(
                    $transaction->points,
                    'expired',
                    null,
                    "Points expired after {$expiryDays} days"
                );
            }
        }
    }
}