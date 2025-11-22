# DNP Database Entity Relationship Diagram

## Overview

This document contains the complete database schema for the DNP multi-vendor nutrition e-commerce platform.

---

## 1. Users & Authentication

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        string password
        string verification_code
    }
    customers {
        bigint id PK
        bigint user_id FK
        string first_name
        string last_name
        string phone
        int total_loyalty_points
        string membership_tier
        string referral_code
        bigint referred_by FK
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
    }
    role_has_permissions {
        bigint permission_id FK
        bigint role_id FK
    }

    users ||--o| customers : "has profile"
    users ||--o{ staff : "can be staff"
    staff }o--|| roles : "has role"
    roles ||--o{ role_has_permissions : "has"
    permissions ||--o{ role_has_permissions : "assigned to"
    customers ||--o{ customers : "referred_by"
```

---

## 2. Products & Catalog

```mermaid
erDiagram
    products {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        bigint brand_id FK
        string name
        decimal unit_price
        int current_stock
        json attributes
        boolean published
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
    }
    product_categories {
        bigint product_id FK
        bigint category_id FK
    }
    product_taxes {
        bigint id PK
        bigint product_id FK
        decimal tax
    }

    products ||--o{ product_stocks : "variants"
    products ||--o{ product_translations : "i18n"
    products ||--o{ product_categories : "categories"
    products ||--o{ product_taxes : "taxes"
```

---

## 3. Categories & Attributes

```mermaid
erDiagram
    categories {
        bigint id PK
        bigint parent_id FK
        int level
        string name
        decimal commission_rate
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
    attribute_values {
        bigint id PK
        bigint attribute_id FK
        string value
    }
    attribute_category {
        bigint category_id FK
        bigint attribute_id FK
    }
    brands {
        bigint id PK
        string name
        string slug
    }

    categories ||--o{ categories : "parent"
    categories ||--o{ category_translations : "i18n"
    categories ||--o{ attribute_category : "attrs"
    attributes ||--o{ attribute_values : "values"
    attributes ||--o{ attribute_category : "cats"
```

---

## 4. Shopping Cart & Orders

```mermaid
erDiagram
    carts {
        bigint id PK
        bigint user_id FK
        string status
        string guest_token
    }
    cart_items {
        bigint id PK
        bigint cart_id FK
        bigint product_id FK
        int quantity
        decimal unit_price
    }
    orders {
        bigint id PK
        bigint user_id FK
        bigint cart_id FK
        string order_number
        string status
        decimal total_amount
        string payment_status
    }
    order_items {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        int quantity
        decimal line_total
    }
    order_status_histories {
        bigint id PK
        bigint order_id FK
        string old_status
        string new_status
    }

    carts ||--o{ cart_items : "contains"
    carts ||--o| orders : "becomes"
    orders ||--o{ order_items : "contains"
    orders ||--o{ order_status_histories : "history"
```

---

## 5. Shipping & Logistics

```mermaid
erDiagram
    shipping_carriers {
        bigint id PK
        string name
        string slug
        json api_config
    }
    shipping_zones {
        bigint id PK
        string name
        json countries
    }
    shipping_methods {
        bigint id PK
        bigint carrier_id FK
        bigint zone_id FK
        string name
        int estimated_days
    }
    shipments {
        bigint id PK
        bigint order_id FK
        bigint carrier_id FK
        string tracking_number
        string status
    }

    shipping_carriers ||--o{ shipping_methods : "provides"
    shipping_zones ||--o{ shipping_methods : "in zone"
    shipping_carriers ||--o{ shipments : "ships"
```

---

## 6. Geographic Data

```mermaid
erDiagram
    zones {
        bigint id PK
        string name
    }
    countries {
        bigint id PK
        string code
        string name
        bigint zone_id FK
    }
    states {
        bigint id PK
        string name
        bigint country_id FK
    }
    cities {
        bigint id PK
        string name
        bigint state_id FK
        decimal cost
    }

    zones ||--o{ countries : "contains"
    countries ||--o{ states : "has"
    states ||--o{ cities : "has"
```

---

## 7. Coupons & Loyalty

```mermaid
erDiagram
    coupons {
        bigint id PK
        string code UK
        string type
        decimal value
        timestamp starts_at
        timestamp ends_at
    }
    coupon_redemptions {
        bigint id PK
        bigint coupon_id FK
        bigint user_id FK
        bigint order_id FK
        decimal discount
    }
    loyalty_points_transactions {
        bigint id PK
        bigint customer_id FK
        bigint order_id FK
        string type
        int points
    }
    vouchers {
        bigint id PK
        string code UK
        bigint customer_id FK
        decimal value
        string status
    }

    coupons ||--o{ coupon_redemptions : "used"
```

---

## 8. Reviews & Wishlists

```mermaid
erDiagram
    reviews {
        bigint id PK
        bigint product_id FK
        bigint user_id FK
        int rating
        text comment
        string status
    }
    wishlists {
        bigint id PK
        bigint user_id FK
        bigint product_id FK
    }
    branches {
        bigint id PK
        json name
        string code
        string address
    }
    product_branch {
        bigint product_id FK
        bigint branch_id FK
    }

    branches ||--o{ product_branch : "products"
```

---

## 9. Content & CMS

```mermaid
erDiagram
    blog_categories {
        bigint id PK
        string category_name
        string slug
    }
    blogs {
        bigint id PK
        bigint category_id FK
        string title
        string slug
    }
    recipe_categories {
        bigint id PK
        string category_name
    }
    recipes {
        bigint id PK
        bigint category_id FK
        string title
        int calories
    }
    posts {
        bigint id PK
        bigint post_type_id FK
        string slug
        bigint author_id FK
    }
    post_types {
        bigint id PK
        json label
    }

    blog_categories ||--o{ blogs : "has"
    recipe_categories ||--o{ recipes : "has"
    post_types ||--o{ posts : "typed"
```

---

## 10. Cross-Domain Relationships (Overview)

```mermaid
erDiagram
    users ||--o| customers : "profile"
    users ||--o{ carts : "shopping"
    users ||--o{ orders : "purchases"
    users ||--o{ products : "sells"

    products ||--o{ cart_items : "added"
    products ||--o{ order_items : "ordered"
    products }o--|| categories : "categorized"
    products }o--|| brands : "branded"

    carts ||--o{ cart_items : "contains"
    carts ||--o| orders : "checkout"

    orders ||--o{ order_items : "items"
    orders ||--o{ shipments : "shipped"

    customers ||--o{ loyalty_points_transactions : "rewards"
    customers ||--o{ vouchers : "redeems"
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
