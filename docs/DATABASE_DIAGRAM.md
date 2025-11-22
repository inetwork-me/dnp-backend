# DNP Database Entity Relationship Diagram

## Overview

This document contains the complete database schema for the DNP multi-vendor nutrition e-commerce platform.

---

## Complete ER Diagram

```mermaid
erDiagram
    %% ==========================================
    %% AUTHENTICATION & USERS
    %% ==========================================

    users {
        bigint id PK
        string name
        string email UK
        string password
        string verification_code
        timestamp created_at
        timestamp updated_at
    }

    customers {
        bigint id PK
        bigint user_id FK
        string first_name
        string last_name
        string gender
        string phone
        text billing_address
        text shipping_address
        int total_loyalty_points
        string membership_tier
        string referral_code
        bigint referred_by FK
        string status
    }

    staff {
        bigint id PK
        bigint user_id FK
        bigint role_id FK
    }

    roles {
        bigint id PK
        string name
        string guard_name
    }

    permissions {
        bigint id PK
        string name
        string guard_name
        string section
    }

    role_has_permissions {
        bigint permission_id FK
        bigint role_id FK
    }

    model_has_roles {
        bigint role_id FK
        string model_type
        bigint model_id
    }

    model_has_permissions {
        bigint permission_id FK
        string model_type
        bigint model_id
    }

    users ||--o| customers : "has profile"
    users ||--o{ staff : "can be staff"
    staff }o--|| roles : "has role"
    roles ||--o{ role_has_permissions : "has"
    permissions ||--o{ role_has_permissions : "assigned to"
    roles ||--o{ model_has_roles : "assigned via"
    permissions ||--o{ model_has_permissions : "assigned via"
    customers ||--o{ customers : "referred_by"

    %% ==========================================
    %% PRODUCTS & CATALOG
    %% ==========================================

    products {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        bigint brand_id FK
        string name
        decimal unit_price
        decimal purchase_price
        int current_stock
        json attributes
        json colors
        json variations
        json multimedia
        boolean published
        timestamp created_at
    }

    product_stocks {
        bigint id PK
        bigint product_id FK
        string variant
        string sku
        decimal price
        int qty
    }

    product_translations {
        bigint id PK
        bigint product_id FK
        string lang
        string name
        text description
    }

    product_categories {
        bigint product_id FK
        bigint category_id FK
    }

    product_taxes {
        bigint id PK
        bigint product_id FK
        bigint tax_id
        decimal tax
        string tax_type
    }

    product_commissions {
        bigint id PK
        bigint product_id FK
        decimal commission
        string commission_type
    }

    product_stock_transactions {
        bigint id PK
        bigint product_id FK
        bigint user_id FK
        bigint order_id FK
        string transaction_type
        int quantity_change
        timestamp created_at
    }

    frequently_bought_products {
        bigint id PK
        bigint product_id FK
        bigint related_product_id FK
    }

    products ||--o{ product_stocks : "has variants"
    products ||--o{ product_translations : "translations"
    products ||--o{ product_categories : "in categories"
    products ||--o{ product_taxes : "has taxes"
    products ||--o{ product_commissions : "has commissions"
    products ||--o{ product_stock_transactions : "stock history"
    products ||--o{ frequently_bought_products : "related products"
    users ||--o{ products : "vendor owns"
    users ||--o{ product_stock_transactions : "performed by"

    %% ==========================================
    %% CATEGORIES & ATTRIBUTES
    %% ==========================================

    categories {
        bigint id PK
        bigint parent_id FK
        int level
        string name
        int order_level
        decimal commission_rate
        timestamp deleted_at
    }

    category_translations {
        bigint id PK
        bigint category_id FK
        string lang
        string name
    }

    attributes {
        bigint id PK
        string name
    }

    attribute_translations {
        bigint id PK
        bigint attribute_id FK
        string lang
        string name
    }

    attribute_values {
        bigint id PK
        bigint attribute_id FK
        string value
        string color_code
    }

    attribute_category {
        bigint category_id FK
        bigint attribute_id FK
    }

    categories ||--o{ categories : "parent-child"
    categories ||--o{ category_translations : "translations"
    categories ||--o{ products : "contains"
    categories ||--o{ product_categories : "has products"
    categories ||--o{ attribute_category : "has attributes"
    attributes ||--o{ attribute_translations : "translations"
    attributes ||--o{ attribute_values : "has values"
    attributes ||--o{ attribute_category : "for categories"

    %% ==========================================
    %% BRANDS
    %% ==========================================

    brands {
        bigint id PK
        string name
        string logo
        string slug
        string meta_title
        string meta_description
    }

    brand_translations {
        bigint id PK
        bigint brand_id FK
        string lang
        string name
        string meta_title
        string meta_description
    }

    brands ||--o{ brand_translations : "translations"
    brands ||--o{ products : "has products"

    %% ==========================================
    %% SHOPPING CART
    %% ==========================================

    carts {
        bigint id PK
        bigint user_id FK
        string status
        string guest_token UK
        timestamp created_at
    }

    cart_items {
        bigint id PK
        bigint cart_id FK
        bigint product_id FK
        bigint branch_id FK
        int quantity
        decimal unit_price
        json options
    }

    users ||--o{ carts : "has carts"
    carts ||--o{ cart_items : "contains"
    products ||--o{ cart_items : "added to cart"

    %% ==========================================
    %% ORDERS
    %% ==========================================

    orders {
        bigint id PK
        bigint user_id FK
        bigint cart_id FK
        bigint coupon_id FK
        string order_number
        string status
        decimal total_amount
        json shipping_address
        string payment_method
        string payment_status
        timestamp created_at
    }

    order_items {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        bigint branch_id FK
        int quantity
        decimal unit_price
        decimal line_total
        json options
    }

    order_status_histories {
        bigint id PK
        bigint order_id FK
        bigint user_id FK
        string old_status
        string new_status
        timestamp created_at
    }

    users ||--o{ orders : "places"
    carts ||--o| orders : "converts to"
    orders ||--o{ order_items : "contains"
    products ||--o{ order_items : "ordered"
    orders ||--o{ order_status_histories : "status changes"
    users ||--o{ order_status_histories : "changed by"
    orders ||--o{ product_stock_transactions : "stock changes"

    %% ==========================================
    %% SHIPPING
    %% ==========================================

    shipping_carriers {
        bigint id PK
        string name
        string slug
        json api_config
        boolean supports_tracking
        boolean supports_labels
        boolean supports_live_rates
    }

    shipping_zones {
        bigint id PK
        string name
        json countries
        boolean is_active
    }

    shipping_methods {
        bigint id PK
        bigint carrier_id FK
        bigint zone_id FK
        string name
        string service_code
        int estimated_days
        string rate_source
    }

    shipments {
        bigint id PK
        bigint order_id FK
        bigint carrier_id FK
        string tracking_number
        string status
        json carrier_response
        json shipping_label
        timestamp shipped_at
        timestamp delivered_at
    }

    pickups {
        bigint id PK
        bigint vendor_id FK
        string pickup_guid
        string reference_number
        string status
        json pickup_address
        string contact_person
        date pickup_date
    }

    shipping_carriers ||--o{ shipping_methods : "provides"
    shipping_zones ||--o{ shipping_methods : "available in"
    shipping_carriers ||--o{ shipments : "ships via"
    orders ||--o{ shipments : "shipped as"
    users ||--o{ pickups : "vendor pickups"

    %% ==========================================
    %% GEOGRAPHIC
    %% ==========================================

    zones {
        bigint id PK
        string name
        boolean status
    }

    countries {
        bigint id PK
        string code
        string name
        bigint zone_id FK
        boolean status
    }

    states {
        bigint id PK
        string name
        bigint country_id FK
        boolean status
    }

    cities {
        bigint id PK
        string name
        bigint state_id FK
        decimal cost
        boolean status
    }

    city_translations {
        bigint id PK
        bigint city_id FK
        string lang
        string name
    }

    zones ||--o{ countries : "contains"
    countries ||--o{ states : "has states"
    states ||--o{ cities : "has cities"
    cities ||--o{ city_translations : "translations"

    %% ==========================================
    %% COUPONS & DISCOUNTS
    %% ==========================================

    coupons {
        bigint id PK
        string code UK
        string type
        decimal value
        int usage_limit_per_customer
        int usage_limit_global
        timestamp starts_at
        timestamp ends_at
        boolean active
    }

    coupon_redemptions {
        bigint id PK
        bigint coupon_id FK
        bigint user_id FK
        bigint cart_id FK
        bigint order_id FK
        decimal discount
    }

    coupons ||--o{ coupon_redemptions : "redeemed"
    coupons ||--o{ orders : "applied to"
    users ||--o{ coupon_redemptions : "used by"
    carts ||--o{ coupon_redemptions : "applied in"
    orders ||--o{ coupon_redemptions : "finalized in"

    %% ==========================================
    %% LOYALTY & REWARDS
    %% ==========================================

    loyalty_points_transactions {
        bigint id PK
        bigint customer_id FK
        bigint order_id FK
        bigint product_id FK
        string type
        int points
        string description
        json metadata
        bigint created_by FK
    }

    loyalty_settings {
        bigint id PK
        string key
        string value
        string type
        string group
    }

    vouchers {
        bigint id PK
        string code UK
        bigint customer_id FK
        decimal value
        string currency
        int points_used
        string status
        timestamp expires_at
        timestamp used_at
        bigint used_in_order FK
        bigint created_by FK
    }

    customers ||--o{ loyalty_points_transactions : "earns/spends"
    orders ||--o{ loyalty_points_transactions : "from order"
    products ||--o{ loyalty_points_transactions : "from product"
    customers ||--o{ vouchers : "owns"
    orders ||--o{ vouchers : "used in"
    users ||--o{ loyalty_points_transactions : "created by"
    users ||--o{ vouchers : "created by"

    %% ==========================================
    %% WISHLISTS & REVIEWS
    %% ==========================================

    wishlists {
        bigint id PK
        bigint user_id FK
        bigint product_id FK
    }

    reviews {
        bigint id PK
        bigint product_id FK
        bigint user_id FK
        int rating
        text comment
        string status
        boolean viewed
    }

    users ||--o{ wishlists : "has wishlist"
    products ||--o{ wishlists : "wishlisted"
    users ||--o{ reviews : "writes"
    products ||--o{ reviews : "reviewed"

    %% ==========================================
    %% BRANCHES
    %% ==========================================

    branches {
        bigint id PK
        json name
        string code
        string address
        string city
        string phone
        string email
        boolean is_active
        int sort_order
    }

    product_branch {
        bigint product_id FK
        bigint branch_id FK
    }

    branches ||--o{ product_branch : "has products"
    products ||--o{ product_branch : "available at"
    branches ||--o{ cart_items : "items from"
    branches ||--o{ order_items : "fulfilled by"
```

---

## Domain-Specific Diagrams

### 1. User & Authentication Domain

```mermaid
erDiagram
    users ||--o| customers : "has profile"
    users ||--o{ staff : "can be staff"
    users ||--o{ carts : "has carts"
    users ||--o{ orders : "places orders"
    users ||--o{ reviews : "writes reviews"
    users ||--o{ wishlists : "has wishlist"
    users ||--o{ products : "vendor owns"
    users ||--o{ uploads : "uploads files"

    staff }o--|| roles : "has role"
    roles ||--o{ role_has_permissions : "has permissions"
    permissions ||--o{ role_has_permissions : "assigned to roles"

    customers ||--o{ loyalty_points_transactions : "earns points"
    customers ||--o{ vouchers : "owns vouchers"
    customers ||--o{ customers : "referred by"
```

### 2. Product & Catalog Domain

```mermaid
erDiagram
    products ||--o{ product_stocks : "has variants"
    products ||--o{ product_translations : "i18n"
    products ||--o{ product_categories : "categorized"
    products ||--o{ product_taxes : "taxed"
    products ||--o{ product_commissions : "commission"
    products ||--o{ product_branch : "at branches"
    products ||--o{ frequently_bought_products : "related"
    products ||--o{ reviews : "reviewed"
    products ||--o{ wishlists : "wishlisted"
    products ||--o{ cart_items : "in carts"
    products ||--o{ order_items : "ordered"

    categories ||--o{ categories : "parent-child"
    categories ||--o{ category_translations : "i18n"
    categories ||--o{ products : "main category"
    categories ||--o{ product_categories : "products"
    categories ||--o{ attribute_category : "attributes"

    brands ||--o{ brand_translations : "i18n"
    brands ||--o{ products : "branded"

    attributes ||--o{ attribute_translations : "i18n"
    attributes ||--o{ attribute_values : "values"
    attributes ||--o{ attribute_category : "categories"
```

### 3. Order & Shopping Domain

```mermaid
erDiagram
    users ||--o{ carts : "owns"
    carts ||--o{ cart_items : "contains"
    carts ||--o| orders : "converts to"

    orders ||--o{ order_items : "contains"
    orders ||--o{ order_status_histories : "status changes"
    orders ||--o{ shipments : "shipped"
    orders ||--o{ coupon_redemptions : "discounts"
    orders ||--o{ loyalty_points_transactions : "points"
    orders ||--o{ product_stock_transactions : "stock"

    products ||--o{ cart_items : "added"
    products ||--o{ order_items : "ordered"

    coupons ||--o{ coupon_redemptions : "used"
    coupons ||--o{ orders : "applied"

    branches ||--o{ cart_items : "from"
    branches ||--o{ order_items : "fulfilled"
```

### 4. Shipping Domain

```mermaid
erDiagram
    shipping_carriers ||--o{ shipping_methods : "provides"
    shipping_carriers ||--o{ shipments : "ships"

    shipping_zones ||--o{ shipping_methods : "methods"

    orders ||--o{ shipments : "shipped as"

    users ||--o{ pickups : "vendor pickups"

    zones ||--o{ countries : "contains"
    countries ||--o{ states : "has"
    states ||--o{ cities : "has"
    cities ||--o{ city_translations : "i18n"
```

### 5. Content & CMS Domain

```mermaid
erDiagram
    blog_categories ||--o{ blogs : "contains"
    blogs ||--o{ blog_translations : "i18n"

    recipe_categories ||--o{ recipes : "contains"
    recipes ||--o{ recipe_translations : "i18n"

    post_types ||--o{ posts : "typed as"
    posts ||--o{ post_translations : "i18n"
    users ||--o{ posts : "authored"

    media_folders ||--o{ media : "contains"
    media ||--o{ media_tag : "tagged"
    tags ||--o{ media_tag : "tags"

    forms ||--o{ form_fields : "has fields"
    forms ||--o{ form_submissions : "submissions"
```

---

## Table Summary by Domain

| Domain | Tables | Description |
|--------|--------|-------------|
| **Authentication** | users, customers, staff, roles, permissions, role_has_permissions, model_has_roles, model_has_permissions, personal_access_tokens | User management & RBAC |
| **Products** | products, product_stocks, product_translations, product_categories, product_taxes, product_commissions, product_stock_transactions, frequently_bought_products, product_branch | Product catalog |
| **Categories** | categories, category_translations, attributes, attribute_translations, attribute_values, attribute_category | Categorization & attributes |
| **Brands** | brands, brand_translations | Brand management |
| **Shopping** | carts, cart_items | Shopping cart |
| **Orders** | orders, order_items, order_status_histories | Order management |
| **Shipping** | shipping_carriers, shipping_zones, shipping_methods, shipments, pickups, carriers, carrier_ranges, carrier_range_prices | Shipping & logistics |
| **Geographic** | zones, countries, states, cities, city_translations | Location data |
| **Discounts** | coupons, coupon_redemptions | Promotions |
| **Loyalty** | loyalty_points_transactions, loyalty_settings, vouchers | Rewards program |
| **Engagement** | wishlists, reviews | User engagement |
| **Branches** | branches, product_branch | Multi-location |
| **Content** | blogs, blog_categories, blog_translations, posts, post_types, post_translations, post_type_categories, recipes, recipe_categories, recipe_translations | CMS |
| **Media** | uploads, media, media_folders, tags, media_tag | File management |
| **Forms** | forms, form_fields, form_submissions, field_groups, field_group_rules, custom_fields, custom_field_values, blocks | Dynamic forms |
| **Settings** | business_settings, settings, translations, app_translations, languages, currencies, bmi_settings, bmi_records | Configuration |
| **Notifications** | notifications, notification_types, notification_type_translations | Alerts |
| **System** | password_resets, password_reset_tokens, failed_jobs, social_credentials, personal_access_tokens, searches, payku_transactions, payku_payments | System tables |

---

## Key Relationships Summary

### Foreign Key Cascade Behavior

| Relationship | On Delete |
|--------------|-----------|
| users → customers | CASCADE |
| users → carts | CASCADE |
| users → orders | SET NULL |
| users → reviews | CASCADE |
| carts → cart_items | CASCADE |
| orders → order_items | CASCADE |
| orders → shipments | CASCADE |
| categories → category_translations | CASCADE |
| products → product_stock_transactions | CASCADE |
| shipping_carriers → shipments | RESTRICT |
| coupons → coupon_redemptions | CASCADE |
| customers → loyalty_points_transactions | CASCADE |

### Polymorphic Relationships

1. **model_has_roles** - Assigns roles to any model (users, staff, etc.)
2. **model_has_permissions** - Assigns permissions to any model
3. **notifications** - Laravel polymorphic notifications

### Many-to-Many Pivot Tables

| Pivot Table | Table 1 | Table 2 |
|-------------|---------|---------|
| product_categories | products | categories |
| attribute_category | attributes | categories |
| product_branch | products | branches |
| media_tag | media | tags |
| wishlists | users | products |
| role_has_permissions | roles | permissions |

---

## Notes

- All tables use `bigint` for primary keys with auto-increment
- Most tables include `created_at` and `updated_at` timestamps
- Soft deletes (`deleted_at`) on: categories, recipes, recipe_categories, blog_categories, blogs, uploads
- JSON columns used for flexible data: product attributes, addresses, carrier configs, form data
- Multi-language support via `*_translations` tables linked by foreign key
