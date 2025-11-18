# DNP E-commerce Platform - Complete API Documentation

## Base URL
- **Development**: `http://localhost/dnp-backend/api`
- **Production**: `https://your-domain.com/api`

## Authentication
The API uses **Laravel Sanctum** for authentication. Include the token in the Authorization header:
```
Authorization: Bearer {token}
```

## API Versions
- **V1**: Customer-facing endpoints (`/api/v1/`)
- **V2**: Admin dashboard endpoints (`/api/v2/`)

---

## 🔐 Authentication Endpoints

### POST /v1/auth/login
Login user and get access token.

**Request:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response (Success):**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "phone": "+1234567890",
        "email_verified_at": "2025-01-15T10:30:00Z"
    },
    "token": "1|abc123token...",
    "expires_at": "2025-02-15T10:30:00Z"
}
```

**Response (Error):**
```json
{
    "result": false,
    "message": ["Invalid credentials"]
}
```

### POST /v1/auth/signup
Register new user account.

**Request:**
```json
{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "register_by": "email"
}
```

**Response (Success):**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "verification_code": "123456"
    },
    "token": "1|abc123token...",
    "message": "Registration successful"
}
```

### GET /v1/auth/logout
*Requires Authentication*

Logout and revoke current token.

**Response:**
```json
{
    "message": "Successfully logged out"
}
```

### GET /v1/auth/user
*Requires Authentication*

Get current authenticated user details.

**Response:**
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "phone": "+1234567890",
    "email_verified_at": "2025-01-15T10:30:00Z",
    "avatar": "https://example.com/avatar.jpg"
}
```

### POST /v1/auth/password/forget_request
Request password reset code.

**Request:**
```json
{
    "email": "user@example.com"
}
```

**Response:**
```json
{
    "result": true,
    "message": "Reset code sent to your email"
}
```

### POST /v1/auth/password/confirm_reset
Confirm password reset with code.

**Request:**
```json
{
    "email": "user@example.com",
    "code": "123456",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response:**
```json
{
    "result": true,
    "message": "Password reset successfully"
}
```

---

## 🛒 Cart Management

### GET /v1/cart
Get current user's cart with items and totals.

**Response:**
```json
{
    "cart": {
        "id": 1,
        "user_id": 1,
        "status": "open",
        "created_at": "2025-01-15T10:30:00Z"
    },
    "items_count": 3,
    "subtotal": 299.97,
    "coupon": {
        "code": "SAVE20",
        "type": "percentage",
        "value": 20,
        "starts_at": "2025-01-01T00:00:00Z",
        "ends_at": "2025-12-31T23:59:59Z"
    },
    "discount": 59.99,
    "total_price": 239.98
}
```

### POST /v1/cart/items
Add item to cart.

**Request:**
```json
{
    "product_id": 123,
    "quantity": 2,
    "options": {
        "color": "red",
        "size": "large"
    }
}
```

**Response:**
```json
{
    "id": 1,
    "cart_id": 1,
    "product_id": 123,
    "quantity": 2,
    "unit_price": 99.99,
    "options": {
        "color": "red",
        "size": "large"
    },
    "product": {
        "id": 123,
        "name": "Premium T-Shirt",
        "slug": "premium-t-shirt",
        "unit_price": 99.99,
        "thumbnail": "https://example.com/image.jpg"
    }
}
```

### PUT /v1/cart/items/{item}
Update cart item quantity.

**Request:**
```json
{
    "quantity": 3
}
```

**Response:**
```json
{
    "id": 1,
    "quantity": 3,
    "unit_price": 99.99,
    "total": 299.97
}
```

### DELETE /v1/cart/items/{item}
Remove item from cart.

**Response:**
```json
{
    "message": "Item removed from cart"
}
```

### POST /v1/cart/{cart}/apply-coupon
Apply coupon to cart.

**Request:**
```json
{
    "coupon_code": "SAVE20"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Coupon applied successfully",
    "discount": 59.99,
    "total_price": 239.98
}
```

### DELETE /v1/cart/{cart}/remove-coupon
Remove applied coupon from cart.

**Response:**
```json
{
    "success": true,
    "message": "Coupon removed",
    "total_price": 299.97
}
```

---

## 📦 Products

### GET /v1/products
Get products list with filtering and pagination.

**Query Parameters:**
- `count_per_page`: Items per page (default: 10)
- `page`: Page number (default: 1)
- `search`: Search term
- `type`: Product type (simple, physical, digital, bundle, package, session)
- `categories`: Category slugs (comma-separated)
- `price_min`: Minimum price
- `price_max`: Maximum price
- `on_sale`: Filter sale products (true/false)
- `sort`: Sort by (price_low_high, price_high_low, newest, oldest, most_popular)

**Response:**
```json
{
    "data": [
        {
            "id": 123,
            "name": "Premium T-Shirt",
            "slug": "premium-t-shirt",
            "type": "simple",
            "unit_price": 99.99,
            "discount": 10.00,
            "discounted_price": 89.99,
            "thumbnail": "https://example.com/image.jpg",
            "avg_rating": 4.5,
            "brand": {
                "id": 1,
                "name": "Nike",
                "slug": "nike"
            },
            "main_category": {
                "id": 1,
                "name": "Clothing",
                "slug": "clothing"
            }
        }
    ],
    "current_page": 1,
    "per_page": 10,
    "total": 150,
    "last_page": 15
}
```

### GET /v1/products/{id}
Get single product details.

**Response:**
```json
{
    "id": 123,
    "name": "Premium T-Shirt",
    "slug": "premium-t-shirt",
    "type": "simple",
    "unit_price": 99.99,
    "discount": 10.00,
    "discounted_price": 89.99,
    "description": "High quality cotton t-shirt...",
    "thumbnail": "https://example.com/image.jpg",
    "photos": [
        "https://example.com/image1.jpg",
        "https://example.com/image2.jpg"
    ],
    "avg_rating": 4.5,
    "reviews_count": 25,
    "brand": {
        "id": 1,
        "name": "Nike",
        "slug": "nike"
    },
    "category": {
        "id": 1,
        "name": "Clothing",
        "slug": "clothing"
    },
    "attributes": [
        {
            "name": "Color",
            "values": ["Red", "Blue", "Green"]
        },
        {
            "name": "Size",
            "values": ["S", "M", "L", "XL"]
        }
    ],
    "specs": {
        "material": "100% Cotton",
        "care": "Machine wash cold"
    }
}
```

### GET /v1/products/{productId}/related
Get related products.

**Response:**
```json
{
    "data": [
        {
            "id": 124,
            "name": "Cotton Shorts",
            "slug": "cotton-shorts",
            "unit_price": 49.99,
            "thumbnail": "https://example.com/shorts.jpg"
        }
    ]
}
```

### GET /v1/products/random
Get random products for homepage.

**Query Parameters:**
- `limit`: Number of products (default: 8)

**Response:**
```json
{
    "data": [
        {
            "id": 125,
            "name": "Summer Dress",
            "unit_price": 79.99,
            "thumbnail": "https://example.com/dress.jpg"
        }
    ]
}
```

---

## 🛍️ Orders & Checkout

### POST /v1/checkout
Create new order from cart.

**Request:**
```json
{
    "cart_id": 1,
    "billing_address": {
        "line1": "123 Main St",
        "city": "New York",
        "state": "NY",
        "postal_code": "10001",
        "country": "US"
    },
    "shipping_address": {
        "line1": "456 Oak Ave",
        "city": "Brooklyn",
        "state": "NY",
        "postal_code": "11201",
        "country": "US"
    },
    "shipping_method_id": 1,
    "shipping_cost": 15.99,
    "payment_method": "stripe",
    "coupon_code": "SAVE20",
    "voucher_code": "LOYALTY50"
}
```

**Response (Success):**
```json
{
    "order": {
        "id": 1001,
        "order_number": "ORD-20250115-001",
        "status": "pending",
        "total_amount": 255.97,
        "subtotal": 299.97,
        "discount": 59.99,
        "shipping_cost": 15.99,
        "tax": 0.00,
        "payment_status": "pending",
        "created_at": "2025-01-15T10:30:00Z"
    },
    "payment_url": "https://checkout.stripe.com/session123",
    "message": "Order created successfully"
}
```

### GET /v1/orders
*Requires Authentication*

Get user's order history.

**Response:**
```json
{
    "data": [
        {
            "id": 1001,
            "order_number": "ORD-20250115-001",
            "status": "processing",
            "total_amount": 255.97,
            "items_count": 3,
            "created_at": "2025-01-15T10:30:00Z",
            "latest_shipment": {
                "tracking_number": "ARAMEX-20250115-001001",
                "status": "in_transit",
                "carrier": "Aramex"
            }
        }
    ]
}
```

### GET /v1/orders/{order}
*Requires Authentication*

Get single order details.

**Response:**
```json
{
    "id": 1001,
    "order_number": "ORD-20250115-001",
    "status": "processing",
    "total_amount": 255.97,
    "subtotal": 299.97,
    "discount": 59.99,
    "shipping_cost": 15.99,
    "payment_status": "paid",
    "billing_address": {
        "line1": "123 Main St",
        "city": "New York",
        "country": "US"
    },
    "shipping_address": {
        "line1": "456 Oak Ave",
        "city": "Brooklyn",
        "country": "US"
    },
    "items": [
        {
            "id": 1,
            "product_id": 123,
            "quantity": 2,
            "unit_price": 99.99,
            "total": 199.98,
            "product": {
                "name": "Premium T-Shirt",
                "thumbnail": "https://example.com/image.jpg"
            }
        }
    ],
    "created_at": "2025-01-15T10:30:00Z"
}
```

---

## 🚚 Shipping

### POST /v1/shipping/calculate-rates
Calculate shipping rates for cart/address.

**Request:**
```json
{
    "origin": {
        "line1": "Warehouse Street",
        "city": "Cairo",
        "country": "EG",
        "postal_code": "11511"
    },
    "destination": {
        "line1": "123 Customer St",
        "city": "Alexandria",
        "country": "EG",
        "postal_code": "21500"
    },
    "packages": [
        {
            "weight": 2.5,
            "length": 30,
            "width": 20,
            "height": 10
        }
    ],
    "cart_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "rates": [
            {
                "id": "aramex_domestic",
                "name": "Aramex Domestic",
                "carrier": "Aramex",
                "service_code": "DOM",
                "rate": 50.00,
                "currency": "EGP",
                "estimated_days": 2,
                "description": "Standard domestic delivery"
            }
        ]
    }
}
```

### GET /v1/shipments/track/{trackingNumber}
Track shipment by tracking number (public endpoint).

**Response:**
```json
{
    "tracking_number": "ARAMEX-20250115-001001",
    "status": "in_transit",
    "carrier": "Aramex",
    "estimated_delivery": "2025-01-17",
    "tracking_events": [
        {
            "status": "picked_up",
            "description": "Package picked up from warehouse",
            "location": "Cairo, EG",
            "timestamp": "2025-01-15T14:30:00Z"
        },
        {
            "status": "in_transit",
            "description": "Package in transit to destination",
            "location": "Alexandria, EG",
            "timestamp": "2025-01-16T08:15:00Z"
        }
    ]
}
```

---

## 🏷️ Categories & Brands

### GET /v1/categories
Get product categories.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Clothing",
            "slug": "clothing",
            "image": "https://example.com/category.jpg",
            "products_count": 150,
            "children": [
                {
                    "id": 11,
                    "name": "T-Shirts",
                    "slug": "t-shirts",
                    "products_count": 45
                }
            ]
        }
    ]
}
```

### GET /v1/categories/featured
Get featured categories for homepage.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Clothing",
            "slug": "clothing",
            "image": "https://example.com/featured-category.jpg"
        }
    ]
}
```

### GET /v1/brands
Get product brands.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Nike",
            "slug": "nike",
            "logo": "https://example.com/nike-logo.jpg",
            "products_count": 25
        }
    ]
}
```

### GET /v1/brands/top
Get top brands.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Nike",
            "slug": "nike",
            "logo": "https://example.com/nike-logo.jpg"
        }
    ]
}
```

---

## 💳 Coupons & Loyalty

### POST /v1/coupons/apply
Apply coupon code and get discount calculation.

**Request:**
```json
{
    "coupon_code": "SAVE20",
    "subtotal": 299.97
}
```

**Response (Success):**
```json
{
    "valid": true,
    "coupon": {
        "code": "SAVE20",
        "type": "percentage",
        "value": 20,
        "description": "20% off all items"
    },
    "discount": 59.99,
    "final_total": 239.98
}
```

**Response (Invalid):**
```json
{
    "valid": false,
    "message": "Coupon code is invalid or expired"
}
```

### GET /v1/loyalty/summary
*Requires Authentication*

Get user's loyalty points summary.

**Response:**
```json
{
    "current_points": 1250,
    "pending_points": 150,
    "lifetime_earned": 5000,
    "lifetime_redeemed": 3750,
    "tier": "Gold",
    "next_tier": "Platinum",
    "points_to_next_tier": 250,
    "conversion_rate": 100,
    "voucher_value": 12.50
}
```

### GET /v1/loyalty/transactions
*Requires Authentication*

Get loyalty points transaction history.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "type": "earned",
            "points": 150,
            "description": "Order #ORD-20250115-001",
            "status": "completed",
            "created_at": "2025-01-15T10:30:00Z"
        },
        {
            "id": 2,
            "type": "redeemed",
            "points": -500,
            "description": "Converted to voucher",
            "status": "completed",
            "created_at": "2025-01-10T14:20:00Z"
        }
    ]
}
```

### POST /v1/loyalty/convert-to-voucher
*Requires Authentication*

Convert loyalty points to voucher.

**Request:**
```json
{
    "points": 1000
}
```

**Response:**
```json
{
    "voucher": {
        "code": "LOYALTY-ABC123",
        "value": 10.00,
        "expires_at": "2025-04-15T23:59:59Z"
    },
    "points_deducted": 1000,
    "remaining_points": 250
}
```

---

## 🎫 Vouchers

### GET /v1/vouchers
*Requires Authentication*

Get user's vouchers.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "code": "LOYALTY-ABC123",
            "value": 10.00,
            "status": "active",
            "expires_at": "2025-04-15T23:59:59Z",
            "created_at": "2025-01-15T10:30:00Z"
        }
    ]
}
```

### GET /v1/vouchers/active
*Requires Authentication*

Get user's active vouchers only.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "code": "LOYALTY-ABC123",
            "value": 10.00,
            "expires_at": "2025-04-15T23:59:59Z"
        }
    ]
}
```

### POST /v1/vouchers/validate
*Requires Authentication*

Validate voucher code.

**Request:**
```json
{
    "voucher_code": "LOYALTY-ABC123"
}
```

**Response (Valid):**
```json
{
    "valid": true,
    "voucher": {
        "code": "LOYALTY-ABC123",
        "value": 10.00,
        "expires_at": "2025-04-15T23:59:59Z"
    }
}
```

**Response (Invalid):**
```json
{
    "valid": false,
    "message": "Voucher code is invalid or expired"
}
```

---

## ❤️ Wishlist

### GET /v1/auth/wishlist
*Requires Authentication*

Get user's wishlist items.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "product_id": 123,
            "product": {
                "id": 123,
                "name": "Premium T-Shirt",
                "slug": "premium-t-shirt",
                "unit_price": 99.99,
                "thumbnail": "https://example.com/image.jpg"
            },
            "created_at": "2025-01-15T10:30:00Z"
        }
    ]
}
```

### POST /v1/auth/wishlist
*Requires Authentication*

Add product to wishlist.

**Request:**
```json
{
    "product_id": 123
}
```

**Response:**
```json
{
    "id": 1,
    "product_id": 123,
    "message": "Product added to wishlist"
}
```

### DELETE /v1/auth/wishlist/{productId}
*Requires Authentication*

Remove product from wishlist.

**Response:**
```json
{
    "message": "Product removed from wishlist"
}
```

### POST /v1/auth/wishlist/sync
*Requires Authentication*

Sync local wishlist with server.

**Request:**
```json
{
    "product_ids": [123, 124, 125]
}
```

**Response:**
```json
{
    "synced": 3,
    "message": "Wishlist synced successfully"
}
```

### DELETE /v1/auth/wishlist
*Requires Authentication*

Clear entire wishlist.

**Response:**
```json
{
    "message": "Wishlist cleared successfully"
}
```

---

## 📝 Reviews

### GET /v1/reviews
*Requires Authentication*

Get user's reviews.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "product_id": 123,
            "rating": 5,
            "comment": "Great product, highly recommended!",
            "product": {
                "name": "Premium T-Shirt",
                "thumbnail": "https://example.com/image.jpg"
            },
            "created_at": "2025-01-15T10:30:00Z"
        }
    ]
}
```

### POST /v1/reviews
*Requires Authentication*

Submit product review.

**Request:**
```json
{
    "product_id": 123,
    "order_id": 1001,
    "rating": 5,
    "comment": "Great product, highly recommended!"
}
```

**Response:**
```json
{
    "id": 1,
    "product_id": 123,
    "rating": 5,
    "comment": "Great product, highly recommended!",
    "status": "approved",
    "created_at": "2025-01-15T10:30:00Z"
}
```

---

## 🔧 Settings & Configuration

### GET /v1/settings
Get website settings and configuration.

**Response:**
```json
{
    "site_name": "DNP E-commerce",
    "currency": {
        "code": "EGP",
        "symbol": "£",
        "exchange_rate": 1.0
    },
    "shipping_enabled": true,
    "loyalty_enabled": true,
    "reviews_enabled": true,
    "guest_checkout": true,
    "social_login": {
        "google": true,
        "facebook": true,
        "apple": false
    },
    "payment_methods": ["stripe", "paypal", "myfatoorah"],
    "contact": {
        "email": "support@dnp.com",
        "phone": "+20123456789",
        "address": "Cairo, Egypt"
    }
}
```

### GET /v1/languages
Get available languages.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "code": "en",
            "name": "English",
            "flag": "🇺🇸",
            "rtl": false
        },
        {
            "id": 2,
            "code": "ar",
            "name": "العربية",
            "flag": "🇪🇬",
            "rtl": true
        }
    ]
}
```

### GET /v1/currencies
Get available currencies.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "code": "EGP",
            "name": "Egyptian Pound",
            "symbol": "£",
            "exchange_rate": 1.0
        },
        {
            "id": 2,
            "code": "USD",
            "name": "US Dollar",
            "symbol": "$",
            "exchange_rate": 0.032
        }
    ]
}
```

---

## 🏠 Homepage Content

### GET /v1/banners
Get homepage banners.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "title": "Summer Sale",
            "subtitle": "Up to 50% off",
            "image": "https://example.com/banner.jpg",
            "link": "/categories/summer-collection",
            "position": "hero"
        }
    ]
}
```

---

## 📚 Content Management

### GET /v1/posts/{slug}
Get blog post by slug.

**Response:**
```json
{
    "id": 1,
    "title": "Health Benefits of Supplements",
    "slug": "health-benefits-supplements",
    "content": "Lorem ipsum...",
    "excerpt": "Discover the amazing benefits...",
    "featured_image": "https://example.com/blog-image.jpg",
    "author": "Dr. Smith",
    "published_at": "2025-01-15T10:30:00Z",
    "categories": [
        {
            "id": 1,
            "name": "Health",
            "slug": "health"
        }
    ],
    "tags": ["supplements", "health", "nutrition"]
}
```

### GET /v1/post-types/{postType:slug}/posts
Get posts by post type.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "title": "Health Benefits of Supplements",
            "slug": "health-benefits-supplements",
            "excerpt": "Discover the amazing benefits...",
            "featured_image": "https://example.com/blog-image.jpg",
            "published_at": "2025-01-15T10:30:00Z"
        }
    ]
}
```

---

## 📋 Forms

### GET /v1/forms/slug/{slug}
Get form by slug.

**Response:**
```json
{
    "id": 1,
    "title": "Contact Us",
    "slug": "contact-us",
    "description": "Get in touch with our team",
    "fields": [
        {
            "id": 1,
            "name": "name",
            "label": "Full Name",
            "type": "text",
            "required": true,
            "validation": "required|string|max:255"
        },
        {
            "id": 2,
            "name": "email",
            "label": "Email Address",
            "type": "email",
            "required": true,
            "validation": "required|email"
        }
    ]
}
```

### POST /v1/forms/slug/{slug}/submit
Submit form data.

**Request:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "message": "Hello, I have a question about your products..."
}
```

**Response:**
```json
{
    "success": true,
    "message": "Form submitted successfully",
    "submission_id": 123
}
```

---

## 🔍 Search & Filters

### GET /v1/get-search-suggestions
Get search suggestions.

**Query Parameters:**
- `q`: Search query

**Response:**
```json
{
    "suggestions": [
        "Premium T-Shirt",
        "Cotton Shorts",
        "Summer Dress"
    ]
}
```

### GET /v1/filter/categories
Get categories for filtering.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Clothing",
            "slug": "clothing",
            "products_count": 150
        }
    ]
}
```

### GET /v1/filter/brands
Get brands for filtering.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Nike",
            "slug": "nike",
            "products_count": 25
        }
    ]
}
```

---

## 🏥 BMI System

### GET /v1/bmi
Get BMI records.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "height": 175.5,
            "weight": 70.2,
            "bmi": 22.8,
            "category": "Normal",
            "created_at": "2025-01-15T10:30:00Z"
        }
    ]
}
```

### POST /v1/bmi
Create BMI record.

**Request:**
```json
{
    "height": 175.5,
    "weight": 70.2,
    "age": 30,
    "gender": "male"
}
```

**Response:**
```json
{
    "id": 1,
    "height": 175.5,
    "weight": 70.2,
    "bmi": 22.8,
    "category": "Normal",
    "recommendations": [
        {
            "product_id": 123,
            "reason": "Recommended for maintaining healthy weight"
        }
    ]
}
```

### GET /v1/bmi/{bmi}/with-settings
Get BMI record with system settings.

**Response:**
```json
{
    "bmi_record": {
        "id": 1,
        "bmi": 22.8,
        "category": "Normal"
    },
    "settings": [
        {
            "id": 1,
            "name": "Weight Loss Plan",
            "description": "Comprehensive weight management",
            "products": [123, 124, 125]
        }
    ]
}
```

---

## ⚙️ Admin API (V2) - Dashboard

### GET /v2/dashboard/overview
*Requires Authentication*

Get dashboard statistics.

**Response:**
```json
{
    "statistics": {
        "total_customers": 1247,
        "total_orders": 3891,
        "total_products": 156,
        "total_revenue": 89750.25,
        "monthly_revenue": 12450.75,
        "pending_orders": 23,
        "low_stock_products": 8
    },
    "recent_orders": [
        {
            "id": 1001,
            "order_number": "ORD-20250115-001",
            "customer_name": "John Doe",
            "total": 92.17,
            "status": "processing",
            "created_at": "2025-01-15T10:30:00Z"
        }
    ],
    "top_selling_products": [
        {
            "id": 1,
            "name": "Premium Whey Protein",
            "sales_count": 234,
            "revenue": 9416.00,
            "image": "https://example.com/images/protein1.jpg"
        }
    ]
}
```

---

## 👥 Admin API (V2) - User Management

### GET /v2/users
*Requires Authentication*

List users with pagination and filters.

**Query Parameters:**
- `per_page`: Items per page (default: 15)
- `search`: Search by name or email
- `role`: Filter by role
- `status`: Filter by status (active, inactive, banned)

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+1234567890",
            "user_type": "customer",
            "email_verified_at": "2025-01-01T00:00:00Z",
            "status": "active",
            "roles": ["customer"],
            "last_login_at": "2025-01-15T10:30:00Z",
            "orders_count": 5,
            "total_spent": 245.75,
            "created_at": "2025-01-01T00:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 147
    }
}
```

### POST /v2/users
*Requires Authentication*

Create new user.

**Request:**
```json
{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+1987654321",
    "user_type": "customer",
    "roles": ["customer"]
}
```

**Response:**
```json
{
    "result": true,
    "message": "User created successfully",
    "data": {
        "id": 148,
        "name": "Jane Smith",
        "email": "jane@example.com",
        "phone": "+1987654321",
        "user_type": "customer",
        "status": "active",
        "roles": ["customer"],
        "created_at": "2025-01-16T09:00:00Z"
    }
}
```

---

## 🛍️ Admin API (V2) - Product Management

### GET /v2/products
*Requires Authentication*

List products (admin view) with pagination and filters.

**Query Parameters:**
- `per_page`: Items per page (default: 15)
- `search`: Search term
- `type`: Product type filter
- `status`: Status filter (active, inactive, draft)

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Premium Whey Protein",
            "slug": "premium-whey-protein",
            "sku": "DNP-WP-001",
            "type": "physical",
            "status": "active",
            "price": 49.99,
            "sale_price": 39.99,
            "stock_quantity": 50,
            "sales_count": 234,
            "revenue": 9416.00,
            "rating": 4.5,
            "reviews_count": 127,
            "category": {
                "id": 1,
                "name": "Proteins"
            },
            "brand": {
                "id": 1,
                "name": "DNP Nutrition"
            },
            "created_at": "2025-01-01T00:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 11,
        "per_page": 15,
        "total": 156
    }
}
```

### POST /v2/products
*Requires Authentication*

Create new product.

**Request:**
```json
{
    "type": "physical",
    "status": "active",
    "sku": "DNP-CR-001",
    "price": 24.99,
    "sale_price": 19.99,
    "stock_quantity": 100,
    "category_id": 2,
    "brand_id": 1,
    "translations": {
        "en": {
            "name": "Pure Creatine Monohydrate",
            "description": "100% pure creatine monohydrate powder"
        },
        "ar": {
            "name": "كرياتين أحادي الهيدرات النقي",
            "description": "مسحوق كرياتين أحادي الهيدرات نقي 100%"
        }
    },
    "specifications": [
        {
            "name": "Creatine per serving",
            "value": "5g"
        }
    ]
}
```

**Response:**
```json
{
    "result": true,
    "message": "Product created successfully",
    "data": {
        "id": 157,
        "name": "Pure Creatine Monohydrate",
        "slug": "pure-creatine-monohydrate",
        "sku": "DNP-CR-001",
        "type": "physical",
        "status": "active",
        "price": 24.99,
        "sale_price": 19.99,
        "stock_quantity": 100,
        "created_at": "2025-01-16T10:00:00Z"
    }
}
```

---

## 📝 Admin API (V2) - Content Management

### GET /v2/posts
*Requires Authentication*

List blog posts and content.

**Query Parameters:**
- `post_type`: Filter by post type slug
- `status`: Filter by status (published, draft)
- `category_id`: Filter by category

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "title": {
                "en": "10 Benefits of Whey Protein",
                "ar": "10 فوائد لبروتين مصل اللبن"
            },
            "slug": "10-benefits-of-whey-protein",
            "excerpt": {
                "en": "Discover the amazing benefits of whey protein",
                "ar": "اكتشف الفوائد المذهلة لبروتين مصل اللبن"
            },
            "status": "published",
            "featured_image": {
                "url": "https://example.com/images/whey-benefits.jpg",
                "alt": "Benefits of Whey Protein"
            },
            "post_type": {
                "id": 1,
                "name": "Blog Posts",
                "slug": "blog-posts"
            },
            "category": {
                "id": 1,
                "name": "Nutrition"
            },
            "author": {
                "id": 1,
                "name": "Dr. John Smith"
            },
            "published_at": "2025-01-15T09:00:00Z",
            "created_at": "2025-01-14T15:30:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 67
    }
}
```

### POST /v2/posts
*Requires Authentication*

Create new blog post/content.

**Request:**
```json
{
    "post_type": "blog-posts",
    "title": {
        "en": "The Science Behind Creatine Supplementation",
        "ar": "العلم وراء مكملات الكرياتين"
    },
    "slug": "science-behind-creatine-supplementation",
    "category_id": 1,
    "description": {
        "en": "Understanding how creatine works in your body",
        "ar": "فهم كيفية عمل الكرياتين في جسمك"
    },
    "content": {
        "en": "<p>Creatine is a naturally occurring compound...</p>",
        "ar": "<p>الكرياتين هو مركب طبيعي...</p>"
    },
    "featured_image": {
        "url": "https://example.com/images/creatine-science.jpg",
        "alt": "Creatine Science"
    },
    "tags": ["creatine", "science", "supplementation"],
    "status": "published",
    "published_at": "2025-01-16T10:00:00Z"
}
```

**Response:**
```json
{
    "result": true,
    "message": "Post created successfully",
    "data": {
        "id": 68,
        "title": {
            "en": "The Science Behind Creatine Supplementation"
        },
        "slug": "science-behind-creatine-supplementation",
        "status": "published",
        "published_at": "2025-01-16T10:00:00Z",
        "created_at": "2025-01-16T10:00:00Z"
    }
}
```

---

## 🎨 Admin API (V2) - Media Management

### GET /v2/media
*Requires Authentication*

List media files with filters and pagination.

**Query Parameters:**
- `filename`: Filter by filename
- `folder_id`: Filter by folder
- `type`: Filter by type (image, video, document)
- `date_from`: Filter by date range
- `date_to`: Filter by date range

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "filename": "premium-whey-protein.jpg",
            "original_name": "Premium Whey Protein Main Image.jpg",
            "mime_type": "image/jpeg",
            "size": 245760,
            "size_human": "240 KB",
            "type": "image",
            "url": "https://example.com/uploads/media/premium-whey-protein.jpg",
            "thumbnail_url": "https://example.com/uploads/media/thumbs/premium-whey-protein.jpg",
            "alt_text": "Premium Whey Protein Container",
            "dimensions": {
                "width": 800,
                "height": 600
            },
            "folder": {
                "id": 1,
                "name": "Product Images",
                "path": "products"
            },
            "tags": [
                {"id": 1, "name": "product"},
                {"id": 2, "name": "protein"}
            ],
            "uploaded_by": {
                "id": 1,
                "name": "Admin User"
            },
            "created_at": "2025-01-15T14:30:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 15,
        "per_page": 20,
        "total": 287
    }
}
```

### POST /v2/media
*Requires Authentication*

Upload media files (multipart/form-data).

**Request:**
```json
{
    "files": ["file1.jpg", "file2.png"],
    "folder_id": 1,
    "tags": ["product", "new-arrival"],
    "alt_text": "Product image",
    "caption": "New product showcase"
}
```

**Response:**
```json
{
    "result": true,
    "message": "Files uploaded successfully",
    "data": {
        "uploaded": [
            {
                "id": 288,
                "filename": "new-product-image.jpg",
                "original_name": "New Product Image.jpg",
                "url": "https://example.com/uploads/media/new-product-image.jpg",
                "size": 156789,
                "type": "image",
                "dimensions": {
                    "width": 1200,
                    "height": 800
                }
            }
        ],
        "failed": [],
        "summary": {
            "uploaded_count": 1,
            "failed_count": 0,
            "total_size": "153 KB"
        }
    }
}
```

---

## ⚙️ Admin API (V2) - Settings Management

### GET /v2/settings
*Requires Authentication*

Get all system settings.

**Response:**
```json
{
    "data": {
        "general": {
            "site_name": "DNP Nutrition Store",
            "site_description": "Premium supplements for fitness enthusiasts",
            "site_logo": "https://example.com/logo.png",
            "timezone": "America/New_York"
        },
        "contact": {
            "email": "info@dnpnutrition.com",
            "phone": "+1-800-DNP-NUTR",
            "address": "123 Fitness St, Health City, HC 12345",
            "social_media": {
                "facebook": "https://facebook.com/dnpnutrition",
                "instagram": "https://instagram.com/dnpnutrition"
            }
        },
        "ecommerce": {
            "currency": "USD",
            "currency_symbol": "$",
            "tax_rate": 8.25,
            "shipping": {
                "free_shipping_threshold": 75.00,
                "standard_shipping_cost": 9.99
            },
            "payment_methods": {
                "stripe": {
                    "enabled": true,
                    "test_mode": false
                },
                "paypal": {
                    "enabled": true,
                    "test_mode": false
                }
            }
        }
    }
}
```

### PATCH /v2/settings
*Requires Authentication*

Update single setting.

**Request:**
```json
{
    "key": "general.site_name",
    "value": {
        "en": "DNP Nutrition Store",
        "ar": "متجر دي إن بي للتغذية"
    }
}
```

**Response:**
```json
{
    "result": true,
    "message": "Setting updated successfully",
    "data": {
        "key": "general.site_name",
        "value": {
            "en": "DNP Nutrition Store",
            "ar": "متجر دي إن بي للتغذية"
        },
        "updated_at": "2025-01-16T11:00:00Z"
    }
}
```

---

## 🎫 Admin API (V2) - Coupon Management

### GET /v2/coupons
*Requires Authentication*

List coupons with pagination.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "code": "SAVE20",
            "type": "percentage",
            "value": 20,
            "description": "20% off all items",
            "minimum_amount": 50.00,
            "maximum_discount": 100.00,
            "usage_limit": 100,
            "used_count": 25,
            "status": "active",
            "starts_at": "2025-01-01T00:00:00Z",
            "expires_at": "2025-12-31T23:59:59Z",
            "created_at": "2025-01-01T00:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 34
    }
}
```

### POST /v2/coupons
*Requires Authentication*

Create new coupon.

**Request:**
```json
{
    "code": "NEWUSER15",
    "type": "percentage",
    "value": 15,
    "description": "15% off for new users",
    "minimum_amount": 25.00,
    "maximum_discount": 50.00,
    "usage_limit": 200,
    "user_limit": 1,
    "status": "active",
    "starts_at": "2025-02-01T00:00:00Z",
    "expires_at": "2025-02-28T23:59:59Z"
}
```

**Response:**
```json
{
    "result": true,
    "message": "Coupon created successfully",
    "data": {
        "id": 35,
        "code": "NEWUSER15",
        "type": "percentage",
        "value": 15,
        "status": "active",
        "created_at": "2025-01-16T12:00:00Z"
    }
}
```

---

## 📦 Admin API (V2) - Order Management

### GET /v2/orders
*Requires Authentication*

List orders with pagination and filters.

**Query Parameters:**
- `status`: Filter by order status
- `date_from`: Filter by date range
- `date_to`: Filter by date range
- `customer_id`: Filter by customer

**Response:**
```json
{
    "data": [
        {
            "id": 1001,
            "order_number": "ORD-20250115-001",
            "status": "processing",
            "payment_status": "paid",
            "total_amount": 255.97,
            "subtotal": 299.97,
            "discount": 59.99,
            "shipping_cost": 15.99,
            "items_count": 3,
            "customer": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            },
            "shipping_address": {
                "line1": "456 Oak Ave",
                "city": "Brooklyn",
                "country": "US"
            },
            "created_at": "2025-01-15T10:30:00Z",
            "updated_at": "2025-01-15T14:20:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 25,
        "per_page": 15,
        "total": 367
    }
}
```

### GET /v2/orders/{order}
*Requires Authentication*

Get single order details (admin view).

**Response:**
```json
{
    "id": 1001,
    "order_number": "ORD-20250115-001",
    "status": "processing",
    "payment_status": "paid",
    "total_amount": 255.97,
    "subtotal": 299.97,
    "discount": 59.99,
    "shipping_cost": 15.99,
    "tax": 0.00,
    "customer": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+1234567890"
    },
    "billing_address": {
        "line1": "123 Main St",
        "city": "New York",
        "country": "US"
    },
    "shipping_address": {
        "line1": "456 Oak Ave",
        "city": "Brooklyn",
        "country": "US"
    },
    "items": [
        {
            "id": 1,
            "product_id": 123,
            "product_name": "Premium T-Shirt",
            "quantity": 2,
            "unit_price": 99.99,
            "total": 199.98,
            "product": {
                "id": 123,
                "name": "Premium T-Shirt",
                "sku": "TS-001",
                "thumbnail": "https://example.com/image.jpg"
            }
        }
    ],
    "payment_details": {
        "method": "stripe",
        "transaction_id": "pi_abc123",
        "amount_paid": 255.97,
        "paid_at": "2025-01-15T10:35:00Z"
    },
    "shipments": [
        {
            "id": 1,
            "tracking_number": "ARAMEX-20250115-001001",
            "carrier": "Aramex",
            "status": "in_transit",
            "shipped_at": "2025-01-15T16:00:00Z"
        }
    ],
    "status_history": [
        {
            "status": "pending",
            "changed_at": "2025-01-15T10:30:00Z",
            "note": "Order placed"
        },
        {
            "status": "processing",
            "changed_at": "2025-01-15T14:20:00Z",
            "note": "Payment confirmed"
        }
    ],
    "created_at": "2025-01-15T10:30:00Z",
    "updated_at": "2025-01-15T14:20:00Z"
}
```

### PUT /v2/orders/{order}/status
*Requires Authentication*

Update order status.

**Request:**
```json
{
    "status": "shipped",
    "note": "Order shipped via Aramex"
}
```

**Response:**
```json
{
    "result": true,
    "message": "Order status updated successfully",
    "data": {
        "id": 1001,
        "status": "shipped",
        "updated_at": "2025-01-16T09:00:00Z"
    }
}
```

---

## 🚚 Admin API (V2) - Shipping Management

### GET /v2/admin/shipping/carriers
*Requires Authentication*

List shipping carriers.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Aramex",
            "slug": "aramex",
            "description": "International express delivery service",
            "status": "active",
            "supports_live_rates": true,
            "supports_tracking": true,
            "configuration": {
                "api_endpoint": "https://ws.aramex.net/ShippingAPI.V2/",
                "username": "testingapi@aramex.com",
                "account_number": "987654",
                "test_mode": true
            },
            "services": [
                {
                    "code": "DOM",
                    "name": "Domestic Delivery",
                    "description": "Standard domestic delivery"
                },
                {
                    "code": "INT",
                    "name": "International Express",
                    "description": "International express delivery"
                }
            ],
            "created_at": "2025-01-01T00:00:00Z"
        }
    ]
}
```

### POST /v2/admin/shipping/carriers
*Requires Authentication*

Create new shipping carrier.

**Request:**
```json
{
    "name": "DHL Express",
    "slug": "dhl-express",
    "description": "Global express delivery service",
    "status": "active",
    "supports_live_rates": true,
    "supports_tracking": true,
    "configuration": {
        "api_endpoint": "https://express.api.dhl.com",
        "api_key": "your-dhl-api-key",
        "account_number": "123456789",
        "test_mode": true
    },
    "services": [
        {
            "code": "EXPRESS",
            "name": "DHL Express Worldwide",
            "description": "Fast international delivery"
        }
    ]
}
```

**Response:**
```json
{
    "result": true,
    "message": "Shipping carrier created successfully",
    "data": {
        "id": 2,
        "name": "DHL Express",
        "slug": "dhl-express",
        "status": "active",
        "created_at": "2025-01-16T11:00:00Z"
    }
}
```

### POST /v2/admin/shipping/carriers/{carrier}/test-connection
*Requires Authentication*

Test carrier API connection.

**Response (Success):**
```json
{
    "result": true,
    "message": "Connection test successful",
    "data": {
        "status": "connected",
        "response_time": "1.2s",
        "api_version": "v2.0",
        "last_tested": "2025-01-16T12:00:00Z"
    }
}
```

**Response (Failure):**
```json
{
    "result": false,
    "message": "Connection test failed",
    "errors": [
        "Invalid API credentials",
        "Connection timeout"
    ],
    "data": {
        "status": "failed",
        "error_code": "AUTH_FAILED",
        "last_tested": "2025-01-16T12:00:00Z"
    }
}
```

---

## 📊 Status Codes & Error Handling

### HTTP Status Codes
- `200` - OK (Success)
- `201` - Created (Resource created)
- `400` - Bad Request (Validation error)
- `401` - Unauthorized (Authentication required)
- `403` - Forbidden (Permission denied)
- `404` - Not Found (Resource not found)
- `422` - Unprocessable Entity (Validation failed)
- `500` - Internal Server Error

### Error Response Format
```json
{
    "result": false,
    "message": "Error description",
    "errors": {
        "field_name": ["Field specific error message"]
    }
}
```

### Validation Error Example
```json
{
    "result": false,
    "message": "The given data was invalid",
    "errors": {
        "email": ["The email field is required"],
        "password": ["The password must be at least 6 characters"]
    }
}
```

---

## 🔗 API Rate Limiting
- **Rate Limit**: 60 requests per minute per IP
- **Headers**:
  - `X-RateLimit-Limit`: Total requests allowed
  - `X-RateLimit-Remaining`: Remaining requests
  - `X-RateLimit-Reset`: Reset timestamp

---

## 📱 Mobile App Considerations
- All endpoints support mobile app integration
- Use `app_language` middleware for localization
- Device token support for push notifications via `device_token` field
- Optimized image URLs for different screen sizes

---

## 🔧 Development Notes
- Base controller handles common response formatting
- Uses Laravel Resource Collections for consistent API responses
- Supports multiple languages via Laravel's localization
- File uploads handled through `/api/v2/media` endpoints
- Real-time shipping integration with Aramex API
- Loyalty points system with automatic calculations
- Guest checkout support with automatic user creation

---

*This documentation covers the main DNP E-commerce Platform API endpoints. For additional details or custom implementations, refer to the source code or contact the development team.*