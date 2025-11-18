<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mapping of old permission names to new dot notation format
        $conversionMap = [
            // Brands
            'view_all_brands' => 'brands.view',
            'add_brand' => 'brands.create',
            'edit_brand' => 'brands.edit',
            'delete_brand' => 'brands.delete',
            'brand_bulk_upload' => 'brands.bulk_upload',

            // Products
            'show_all_products' => 'products.view',
            'show_in_house_products' => 'products.view_inhouse',
            'show_seller_products' => 'products.view_seller',
            'add_new_product' => 'products.create',
            'product_edit' => 'products.edit',
            'product_duplicate' => 'products.duplicate',
            'product_delete' => 'products.delete',
            'product_bulk_import' => 'products.bulk_import',
            'product_bulk_export' => 'products.bulk_export',

            // Digital Products
            'show_digital_products' => 'digital_products.view',
            'add_digital_product' => 'digital_products.create',
            'edit_digital_product' => 'digital_products.edit',
            'delete_digital_product' => 'digital_products.delete',
            'download_digital_product' => 'digital_products.download',

            // Service Products
            'show_service_products' => 'service_products.view',
            'add_service_product' => 'service_products.create',
            'edit_service_product' => 'service_products.edit',
            'delete_service_product' => 'service_products.delete',

            // Product Categories
            'view_product_categories' => 'categories.view',
            'add_product_category' => 'categories.create',
            'edit_product_category' => 'categories.edit',
            'delete_product_category' => 'categories.delete',

            // Product Attributes
            'view_product_attributes' => 'attributes.view',
            'add_product_attribute' => 'attributes.create',
            'edit_product_attribute' => 'attributes.edit',
            'delete_product_attribute' => 'attributes.delete',

            // Product Attribute Values
            'view_product_attribute_values' => 'attribute_values.view',
            'add_product_attribute_values' => 'attribute_values.create',
            'edit_product_attribute_value' => 'attribute_values.edit',
            'delete_product_attribute_value' => 'attribute_values.delete',

            // Colors
            'view_colors' => 'colors.view',
            'add_color' => 'colors.create',
            'edit_color' => 'colors.edit',
            'delete_color' => 'colors.delete',

            // Product Reviews
            'view_product_reviews' => 'reviews.view',
            'publish_product_review' => 'reviews.publish',

            // Orders
            'view_all_orders' => 'orders.view',
            'view_inhouse_orders' => 'orders.view_inhouse',
            'view_seller_orders' => 'orders.view_seller',
            'view_pickup_point_orders' => 'orders.view_pickup_point',
            'view_order_details' => 'orders.view_details',
            'update_order_payment_status' => 'orders.update_payment_status',
            'update_order_delivery_status' => 'orders.update_delivery_status',
            'delete_order' => 'orders.delete',
            'export_order' => 'orders.export',

            // Customers
            'view_all_customers' => 'customers.view',
            'login_as_customer' => 'customers.login_as',
            'ban_customer' => 'customers.ban',
            'delete_customer' => 'customers.delete',

            // Sellers
            'view_all_seller' => 'sellers.view',
            'view_seller_profile' => 'sellers.view_profile',
            'login_as_seller' => 'sellers.login_as',
            'pay_to_seller' => 'sellers.pay',
            'seller_payment_history' => 'sellers.payment_history',
            'edit_seller' => 'sellers.edit',
            'delete_seller' => 'sellers.delete',
            'ban_seller' => 'sellers.ban',
            'approve_seller' => 'sellers.approve',
            'view_seller_payout_requests' => 'sellers.view_payout_requests',
            'seller_commission_configuration' => 'sellers.commission_config',
            'seller_verification_form_configuration' => 'sellers.verification_config',

            // Coupons
            'view_all_coupons' => 'coupons.view',
            'add_coupon' => 'coupons.create',
            'edit_coupon' => 'coupons.edit',
            'delete_coupon' => 'coupons.delete',

            // Blog
            'view_blogs' => 'blogs.view',
            'add_blog' => 'blogs.create',
            'edit_blog' => 'blogs.edit',
            'delete_blog' => 'blogs.delete',
            'publish_blog' => 'blogs.publish',

            // Blog Categories
            'view_blog_categories' => 'blog_categories.view',
            'add_blog_category' => 'blog_categories.create',
            'edit_blog_category' => 'blog_categories.edit',
            'delete_blog_category' => 'blog_categories.delete',

            // Flash Deals
            'view_all_flash_deals' => 'flash_deals.view',
            'add_flash_deal' => 'flash_deals.create',
            'edit_flash_deal' => 'flash_deals.edit',
            'delete_flash_deal' => 'flash_deals.delete',
            'publish_flash_deal' => 'flash_deals.publish',
            'featured_flash_deal' => 'flash_deals.feature',

            // Newsletter
            'send_newsletter' => 'newsletter.send',
            'view_all_subscribers' => 'newsletter.view_subscribers',
            'delete_subscriber' => 'newsletter.delete_subscriber',

            // Support
            'view_all_support_tickets' => 'support.view_tickets',
            'reply_to_support_tickets' => 'support.reply_tickets',
            'view_all_product_queries' => 'support.view_queries',
            'reply_to_product_queries' => 'support.reply_queries',

            // Website Setup
            'header_setup' => 'website.header_setup',
            'footer_setup' => 'website.footer_setup',
            'website_appearance' => 'website.appearance',
            'view_all_website_pages' => 'website.view_pages',
            'add_website_page' => 'website.create_page',
            'edit_website_page' => 'website.edit_page',
            'delete_website_page' => 'website.delete_page',

            // Settings
            'general_settings' => 'settings.general',
            'features_activation' => 'settings.features',
            'language_setup' => 'settings.language',
            'currency_setup' => 'settings.currency',
            'vat_&_tax_setup' => 'settings.vat_tax',
            'pickup_point_setup' => 'settings.pickup_point',
            'smtp_settings' => 'settings.smtp',
            'payment_methods_configurations' => 'settings.payment_methods',
            'order_configuration' => 'settings.order',
            'file_system_&_cache_configuration' => 'settings.filesystem_cache',
            'social_media_logins' => 'settings.social_logins',
            'facebook_chat' => 'settings.facebook_chat',
            'facebook_comment' => 'settings.facebook_comment',
            'analytics_tools_configuration' => 'settings.analytics',
            'google_recaptcha_configuration' => 'settings.recaptcha',
            'google_map_setting' => 'settings.google_map',
            'google_firebase_setting' => 'settings.firebase',
            'shipping_configuration' => 'settings.shipping',
            'shipping_country_setting' => 'settings.shipping_country',
            'manage_shipping_states' => 'settings.shipping_states',
            'manage_shipping_cities' => 'settings.shipping_cities',

            // Staff & Roles
            'view_all_staffs' => 'staff.view',
            'add_staff' => 'staff.create',
            'edit_staff' => 'staff.edit',
            'delete_staff' => 'staff.delete',
            'view_staff_roles' => 'roles.view',
            'add_staff_role' => 'roles.create',
            'edit_staff_role' => 'roles.edit',
            'delete_staff_role' => 'roles.delete',

            // System
            'system_update' => 'system.update',
            'server_status' => 'system.server_status',
            'manage_addons' => 'system.manage_addons',
            'admin_dashboard' => 'dashboard.view',

            // POS
            'pos_manager' => 'pos.manage',
            'pos_configuration' => 'pos.configuration',

            // Auction
            'view_all_auction_products' => 'auction_products.view',
            'view_inhouse_auction_products' => 'auction_products.view_inhouse',
            'view_seller_auction_products' => 'auction_products.view_seller',
            'add_auction_product' => 'auction_products.create',
            'edit_auction_product' => 'auction_products.edit',
            'delete_auction_product' => 'auction_products.delete',
            'view_auction_product_bids' => 'auction_bids.view',
            'delete_auction_product_bids' => 'auction_bids.delete',
            'view_auction_product_orders' => 'auction_orders.view',

            // Wholesale
            'view_all_wholesale_products' => 'wholesale_products.view',
            'view_inhouse_wholesale_products' => 'wholesale_products.view_inhouse',
            'view_sellers_wholesale_products' => 'wholesale_products.view_seller',
            'add_wholesale_product' => 'wholesale_products.create',
            'edit_wholesale_product' => 'wholesale_products.edit',
            'delete_wholesale_product' => 'wholesale_products.delete',

            // Delivery Boy
            'view_all_delivery_boy' => 'delivery_boys.view',
            'add_delivery_boy' => 'delivery_boys.create',
            'edit_delivery_boy' => 'delivery_boys.edit',
            'ban_delivery_boy' => 'delivery_boys.ban',
            'collect_from_delivery_boy' => 'delivery_boys.collect',
            'pay_to_delivery_boy' => 'delivery_boys.pay',
            'delivery_boy_payment_history' => 'delivery_boys.payment_history',
            'collected_histories_from_delivery_boy' => 'delivery_boys.collected_histories',
            'order_cancle_request_by_delivery_boy' => 'delivery_boys.cancel_requests',
            'delivery_boy_configuration' => 'delivery_boys.configuration',
            'assign_delivery_boy_for_orders' => 'delivery_boys.assign',

            // Refund
            'view_refund_requests' => 'refunds.view',
            'accept_refund_request' => 'refunds.accept',
            'reject_refund_request' => 'refunds.reject',
            'view_approved_refund_requests' => 'refunds.view_approved',
            'view_rejected_refund_requests' => 'refunds.view_rejected',
            'refund_request_configuration' => 'refunds.configuration',

            // Affiliate
            'affiliate_registration_form_config' => 'affiliate.registration_config',
            'affiliate_configurations' => 'affiliate.configuration',
            'view_affiliate_users' => 'affiliate.view_users',
            'pay_to_affiliate_user' => 'affiliate.pay_user',
            'affiliate_users_payment_history' => 'affiliate.payment_history',
            'view_all_referral_users' => 'affiliate.view_referrals',
            'view_affiliate_withdraw_requests' => 'affiliate.view_withdrawals',
            'accept_affiliate_withdraw_requests' => 'affiliate.accept_withdrawal',
            'reject_affiliate_withdraw_request' => 'affiliate.reject_withdrawal',
            'view_affiliate_logs' => 'affiliate.view_logs',

            // Manual Payments
            'view_all_manual_payment_methods' => 'manual_payments.view',
            'add_manual_payment_method' => 'manual_payments.create',
            'edit_manual_payment_method' => 'manual_payments.edit',
            'delete_manual_payment_method' => 'manual_payments.delete',

            // Offline Payments
            'view_all_offline_wallet_recharges' => 'offline_payments.view_wallet_recharges',
            'approve_offline_wallet_recharge' => 'offline_payments.approve_wallet_recharge',
            'view_all_offline_customer_package_payments' => 'offline_payments.view_customer_packages',
            'approve_offline_customer_package_payment' => 'offline_payments.approve_customer_package',
            'view_all_offline_seller_package_payments' => 'offline_payments.view_seller_packages',
            'approve_offline_seller_package_payment' => 'offline_payments.approve_seller_package',

            // Club Points
            'club_point_configurations' => 'club_points.configuration',
            'set_club_points' => 'club_points.set',
            'view_users_club_points' => 'club_points.view_users',

            // OTP
            'otp_configurations' => 'otp.configuration',
            'sms_templates' => 'otp.sms_templates',
            'sms_providers_configurations' => 'otp.sms_providers',

            // Payment Gateways
            'asian_payment_gateway_configuration' => 'payment_gateways.asian_config',
            'african_pg_configuration' => 'payment_gateways.african_config',
            'african_pg_credentials_configuration' => 'payment_gateways.african_credentials',

            // Seller Packages
            'view_all_seller_packages' => 'seller_packages.view',
            'add_seller_package' => 'seller_packages.create',
            'edit_seller_package' => 'seller_packages.edit',
            'delete_seller_package' => 'seller_packages.delete',

            // Bulk SMS
            'send_bulk_sms' => 'sms.send_bulk',

            // Shipping Advanced
            'manage_zones' => 'shipping.manage_zones',
            'manage_carriers' => 'shipping.manage_carriers',

            // Product Conversations
            'view_all_product_conversations' => 'product_conversations.view',
            'reply_to_product_conversations' => 'product_conversations.reply',
            'delete_product_conversations' => 'product_conversations.delete',

            // Homepage
            'select_homepage' => 'homepage.select',

            // Size Charts
            'view_size_charts' => 'size_charts.view',
            'add_size_charts' => 'size_charts.create',
            'edit_size_charts' => 'size_charts.edit',
            'delete_size_charts' => 'size_charts.delete',

            // Measurement Points
            'view_measurement_points' => 'measurement_points.view',
            'add_measurement_points' => 'measurement_points.create',
            'edit_measurement_points' => 'measurement_points.edit',
            'delete_measurement_points' => 'measurement_points.delete',

            // Authentication
            'authentication_layout_settings' => 'auth.layout_settings',

            // Discounts
            'set_category_wise_discount' => 'discounts.set_category_wise',

            // Dynamic Popups
            'view_all_dynamic_popups' => 'popups.view',
            'add_dynamic_popups' => 'popups.create',
            'edit_dynamic_popups' => 'popups.edit',
            'delete_dynamic_popups' => 'popups.delete',
            'publish_dynamic_popups' => 'popups.publish',

            // Custom Alerts
            'view_all_custom_alerts' => 'alerts.view',
            'add_custom_alerts' => 'alerts.create',
            'edit_custom_alerts' => 'alerts.edit',
            'delete_custom_alerts' => 'alerts.delete',
            'publish_custom_alerts' => 'alerts.publish',

            // Reports
            'in_house_product_sale_report' => 'reports.inhouse_sales',
            'seller_products_sale_report' => 'reports.seller_sales',
            'products_stock_report' => 'reports.stock',
            'product_wishlist_report' => 'reports.wishlist',
            'user_search_report' => 'reports.user_search',
            'commission_history_report' => 'reports.commission_history',
            'wallet_transaction_report' => 'reports.wallet_transactions',

            // Classified Products
            'view_classified_products' => 'classified.view',
            'publish_classified_product' => 'classified.publish',
            'delete_classified_product' => 'classified.delete',
            'view_classified_packages' => 'classified_packages.view',
            'add_classified_package' => 'classified_packages.create',
            'edit_classified_package' => 'classified_packages.edit',
            'delete_classified_package' => 'classified_packages.delete',
        ];

        foreach ($conversionMap as $oldName => $newName) {
            $oldPermission = Permission::where('name', $oldName)->first();

            if ($oldPermission) {
                // Check if new permission already exists
                $existingNew = Permission::where('name', $newName)->first();

                if ($existingNew) {
                    // Merge: transfer all role and user assignments
                    $roles = $oldPermission->roles;
                    foreach ($roles as $role) {
                        if (!$role->hasPermissionTo($newName)) {
                            $role->givePermissionTo($newName);
                        }
                    }

                    $users = $oldPermission->users;
                    foreach ($users as $user) {
                        if (!$user->hasPermissionTo($newName)) {
                            $user->givePermissionTo($newName);
                        }
                    }

                    // Delete old permission
                    $oldPermission->delete();
                } else {
                    // Rename the permission
                    $oldPermission->name = $newName;
                    $oldPermission->is_legacy = false;
                    $oldPermission->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse is complex and not recommended
        // If you need to rollback, restore from backup
    }
};
