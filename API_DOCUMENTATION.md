# 🚀 DNP Backend API Documentation

<div align="center">

![API Version](https://img.shields.io/badge/API%20Version-V1%20%7C%20V2-blue?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=for-the-badge&logo=laravel)
![Authentication](https://img.shields.io/badge/Auth-Sanctum-green?style=for-the-badge)

</div>

## 📋 Overview
The DNP Backend is a Laravel-based e-commerce/CMS system focused on supplement/nutrition products. It provides two API versions (V1 and V2) with comprehensive functionality for product management, content management, user management, and e-commerce operations.

> **🌐 Base URL**: `https://your-domain.com/api/`

---

## 🔐 Authentication

<div align="center">

| Method | Type | Token Required |
|---------|------|----------------|
| 🔑 **Sanctum** | Bearer Token | Yes (Most V2 endpoints) |
| 🌐 **Public** | None | No (Most V1 endpoints) |

</div>

**📝 Authentication Header**: 
```http
Authorization: Bearer {your-token-here}
```

---

# 🎯 API V1 Endpoints (Public)

<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 10px; color: white; margin: 20px 0;">
<h2 style="margin: 0; color: white;">🔓 V1 API - Public & Customer Endpoints</h2>
<p style="margin: 5px 0 0 0; opacity: 0.9;">Consumer-facing API for mobile apps and public websites</p>
</div>

## 👤 Authentication & User Management

### 🔑 POST `/v1/auth/login`
<div style="background: #e8f5e8; padding: 15px; border-left: 4px solid #4caf50; margin: 10px 0;">

**Purpose**: User login (email/phone)  
**🔒 Authentication**: ❌ No  

**📤 Request Body**:
```json
{
  "email": "user@example.com",
  "password": "password123",
  "login_by": "email"
}
```

**📥 Success Response (200)**:
```json
{
  "result": true,
  "message": "Successfully logged in",
  "access_token": "1|abc123def456ghi789...",
  "token_type": "Bearer",
  "expires_at": "2024-12-31T23:59:59.000000Z",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "phone": "+1234567890",
    "user_type": "customer",
    "email_verified_at": "2024-01-01T00:00:00.000000Z",
    "avatar": "https://example.com/avatars/user1.jpg",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

**❌ Error Response (401)**:
```json
{
  "result": false,
  "message": "Invalid credentials",
  "errors": [
    "The provided credentials are incorrect."
  ]
}
```

</div>

### ✍️ POST `/v1/auth/signup`
<div style="background: #e8f5e8; padding: 15px; border-left: 4px solid #4caf50; margin: 10px 0;">

**Purpose**: User registration  
**🔒 Authentication**: ❌ No  

**📤 Request Body**:
```json
{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+1234567890"
}
```

**📥 Success Response (201)**:
```json
{
  "result": true,
  "message": "Registration successful. Please verify your email.",
  "user": {
    "id": 2,
    "name": "John Doe",
    "email": "user@example.com",
    "phone": "+1234567890",
    "user_type": "customer",
    "email_verified_at": null,
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

</div>

### 📱 POST `/v1/auth/social-login`
<div style="background: #e8f5e8; padding: 15px; border-left: 4px solid #4caf50; margin: 10px 0;">

**Purpose**: Social media login  
**🔒 Authentication**: ❌ No  

**📤 Request Body**:
```json
{
  "social_provider": "google",
  "access_token": "ya29.abc123def456...",
  "provider": "1234567890"
}
```

**📥 Success Response (200)**:
```json
{
  "result": true,
  "message": "Successfully logged in",
  "access_token": "1|xyz789abc123...",
  "token_type": "Bearer",
  "user": {
    "id": 3,
    "name": "John Doe",
    "email": "john@gmail.com",
    "user_type": "customer",
    "avatar": "https://lh3.googleusercontent.com/abc123",
    "social_provider": "google"
  }
}
```

</div>

### 🚪 GET `/v1/auth/logout`
<div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 10px 0;">

**Purpose**: Logout current user  
**🔒 Authentication**: ✅ Yes (Sanctum)

**📥 Success Response (200)**:
```json
{
  "result": true,
  "message": "Successfully logged out"
}
```

</div>

### 👥 GET `/v1/auth/user`
<div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 10px 0;">

**Purpose**: Get current user details  
**🔒 Authentication**: ✅ Yes (Sanctum)

**📥 Success Response (200)**:
```json
{
  "result": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "phone": "+1234567890",
    "user_type": "customer",
    "email_verified_at": "2024-01-01T00:00:00.000000Z",
    "avatar": "https://example.com/avatars/user1.jpg",
    "address": {
      "street": "123 Main St",
      "city": "New York",
      "state": "NY",
      "postal_code": "10001",
      "country": "USA"
    },
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

</div>

### 🔒 POST `/v1/auth/password/forget_request`
<div style="background: #e8f5e8; padding: 15px; border-left: 4px solid #4caf50; margin: 10px 0;">

**Purpose**: Request password reset  
**🔒 Authentication**: ❌ No  

**📤 Request Body**:
```json
{
  "email": "user@example.com"
}
```

**📥 Success Response (200)**:
```json
{
  "result": true,
  "message": "Password reset code sent to your email",
  "data": {
    "reset_code_sent": true,
    "expires_at": "2024-01-01T01:00:00.000000Z"
  }
}
```

</div>

---

## 🛍️ Products & Categories

### 📦 GET `/v1/products`
<div style="background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 10px 0;">

**Purpose**: List products with filtering  
**🔒 Authentication**: ❌ No  

**🔍 Query Parameters**:
- `count_per_page` (optional, default: 10): Items per page
- `type` (optional): Product type filter
- `is_top_selling` (optional): Filter top-selling products
- `category_id` (optional): Filter by category
- `brand_slug` (optional): Filter by brand
- `search` (optional): Search term

**📥 Success Response (200)**:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Premium Whey Protein",
      "slug": "premium-whey-protein",
      "description": "High-quality whey protein isolate",
      "price": 49.99,
      "sale_price": 39.99,
      "currency": "USD",
      "type": "physical",
      "is_top_selling": true,
      "rating": 4.5,
      "reviews_count": 127,
      "stock_quantity": 50,
      "images": [
        {
          "id": 1,
          "url": "https://example.com/images/protein1.jpg",
          "alt": "Premium Whey Protein",
          "is_primary": true
        }
      ],
      "category": {
        "id": 1,
        "name": "Proteins",
        "slug": "proteins"
      },
      "brand": {
        "id": 1,
        "name": "DNP Nutrition",
        "slug": "dnp-nutrition",
        "logo": "https://example.com/brands/dnp.jpg"
      },
      "translations": {
        "en": {
          "name": "Premium Whey Protein",
          "description": "High-quality whey protein isolate"
        },
        "ar": {
          "name": "بروتين مصل اللبن الممتاز",
          "description": "عزل بروتين مصل اللبن عالي الجودة"
        }
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 45,
    "from": 1,
    "to": 10
  }
}
```

</div>

### 🔍 GET `/v1/products/{slug}`
<div style="background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 10px 0;">

**Purpose**: Get product details by slug  
**🔒 Authentication**: ❌ No  

**📥 Success Response (200)**:
```json
{
  "data": {
    "id": 1,
    "name": "Premium Whey Protein",
    "slug": "premium-whey-protein",
    "description": "High-quality whey protein isolate for muscle building",
    "long_description": "Our premium whey protein is sourced from grass-fed cows...",
    "price": 49.99,
    "sale_price": 39.99,
    "currency": "USD",
    "type": "physical",
    "is_top_selling": true,
    "rating": 4.5,
    "reviews_count": 127,
    "stock_quantity": 50,
    "sku": "DNP-WP-001",
    "weight": "2.5kg",
    "dimensions": {
      "length": 15,
      "width": 10,
      "height": 20,
      "unit": "cm"
    },
    "images": [
      {
        "id": 1,
        "url": "https://example.com/images/protein1.jpg",
        "alt": "Premium Whey Protein",
        "is_primary": true
      },
      {
        "id": 2,
        "url": "https://example.com/images/protein1-back.jpg",
        "alt": "Premium Whey Protein Back",
        "is_primary": false
      }
    ],
    "category": {
      "id": 1,
      "name": "Proteins",
      "slug": "proteins",
      "breadcrumb": ["Supplements", "Proteins"]
    },
    "brand": {
      "id": 1,
      "name": "DNP Nutrition",
      "slug": "dnp-nutrition",
      "logo": "https://example.com/brands/dnp.jpg"
    },
    "specifications": [
      {
        "name": "Protein per serving",
        "value": "25g"
      },
      {
        "name": "Servings per container",
        "value": "80"
      }
    ],
    "nutrition_facts": {
      "serving_size": "31g",
      "calories": 120,
      "protein": "25g",
      "carbohydrates": "2g",
      "fat": "1g"
    },
    "reviews": {
      "average_rating": 4.5,
      "total_reviews": 127,
      "rating_breakdown": {
        "5": 85,
        "4": 25,
        "3": 12,
        "2": 3,
        "1": 2
      },
      "recent_reviews": [
        {
          "id": 1,
          "user_name": "Mike Johnson",
          "rating": 5,
          "comment": "Excellent quality protein, mixes well!",
          "created_at": "2024-01-15T10:30:00.000000Z"
        }
      ]
    },
    "frequently_bought_together": [
      {
        "id": 2,
        "name": "Creatine Monohydrate",
        "slug": "creatine-monohydrate",
        "price": 19.99,
        "image": "https://example.com/images/creatine.jpg"
      }
    ],
    "seo": {
      "meta_title": "Premium Whey Protein - High Quality Supplement",
      "meta_description": "Buy premium whey protein isolate. High quality, great taste, fast absorption."
    }
  }
}
```

</div>

### 📂 GET `/v1/categories`
<div style="background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 10px 0;">

**Purpose**: List categories  
**🔒 Authentication**: ❌ No  

**🔍 Query Parameters**:
- `parent_id` (optional): Filter by parent category
- `featured` (optional): Show only featured categories

**📥 Success Response (200)**:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Proteins",
      "slug": "proteins",
      "description": "High-quality protein supplements",
      "level": 0,
      "parent_id": null,
      "featured": true,
      "products_count": 45,
      "image": "https://example.com/categories/proteins.jpg",
      "icon": "https://example.com/icons/protein.svg",
      "children": [
        {
          "id": 2,
          "name": "Whey Protein",
          "slug": "whey-protein",
          "level": 1,
          "parent_id": 1,
          "products_count": 25
        },
        {
          "id": 3,
          "name": "Plant Protein",
          "slug": "plant-protein",
          "level": 1,
          "parent_id": 1,
          "products_count": 20
        }
      ],
      "translations": {
        "en": {
          "name": "Proteins",
          "description": "High-quality protein supplements"
        },
        "ar": {
          "name": "البروتينات",
          "description": "مكملات البروتين عالية الجودة"
        }
      }
    }
  ]
}
```

</div>

---

## 🛒 Shopping Cart

### 🛍️ GET `/v1/cart`
<div style="background: #f3e5f5; padding: 15px; border-left: 4px solid #9c27b0; margin: 10px 0;">

**Purpose**: Get current cart  
**🔒 Authentication**: ❌ No (Uses session/guest cart)

**📥 Success Response (200)**:
```json
{
  "data": {
    "id": "cart_12345",
    "type": "guest",
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "product": {
          "id": 1,
          "name": "Premium Whey Protein",
          "slug": "premium-whey-protein",
          "price": 49.99,
          "sale_price": 39.99,
          "image": "https://example.com/images/protein1.jpg",
          "stock_quantity": 50,
          "in_stock": true
        },
        "quantity": 2,
        "unit_price": 39.99,
        "total_price": 79.98,
        "options": {
          "flavor": "Chocolate",
          "size": "2.5kg"
        }
      }
    ],
    "totals": {
      "subtotal": 79.98,
      "tax": 7.20,
      "shipping": 9.99,
      "discount": 10.00,
      "coupon_discount": 5.00,
      "total": 82.17
    },
    "coupon": {
      "code": "SAVE10",
      "discount_amount": 5.00,
      "discount_type": "fixed"
    },
    "shipping_estimate": {
      "method": "standard",
      "cost": 9.99,
      "estimated_days": "3-5"
    },
    "items_count": 2,
    "created_at": "2024-01-01T10:00:00.000000Z",
    "updated_at": "2024-01-01T10:30:00.000000Z"
  }
}
```

</div>

### ➕ POST `/v1/cart/items`
<div style="background: #f3e5f5; padding: 15px; border-left: 4px solid #9c27b0; margin: 10px 0;">

**Purpose**: Add item to cart  
**🔒 Authentication**: ❌ No  

**📤 Request Body**:
```json
{
  "product_id": 1,
  "quantity": 2,
  "options": {
    "flavor": "Chocolate",
    "size": "2.5kg"
  }
}
```

**📥 Success Response (201)**:
```json
{
  "result": true,
  "message": "Product added to cart successfully",
  "data": {
    "cart_item": {
      "id": 1,
      "product_id": 1,
      "quantity": 2,
      "unit_price": 39.99,
      "total_price": 79.98,
      "options": {
        "flavor": "Chocolate",
        "size": "2.5kg"
      }
    },
    "cart_totals": {
      "subtotal": 79.98,
      "total": 87.17,
      "items_count": 2
    }
  }
}
```

</div>

### ✏️ PUT `/v1/cart/items/{item}`
<div style="background: #f3e5f5; padding: 15px; border-left: 4px solid #9c27b0; margin: 10px 0;">

**Purpose**: Update cart item  
**🔒 Authentication**: ❌ No  

**📤 Request Body**:
```json
{
  "quantity": 3,
  "options": {
    "flavor": "Vanilla",
    "size": "2.5kg"
  }
}
```

**📥 Success Response (200)**:
```json
{
  "result": true,
  "message": "Cart item updated successfully",
  "data": {
    "cart_item": {
      "id": 1,
      "quantity": 3,
      "unit_price": 39.99,
      "total_price": 119.97,
      "options": {
        "flavor": "Vanilla",
        "size": "2.5kg"
      }
    }
  }
}
```

</div>

### 🎟️ POST `/v1/cart/{cart}/apply-coupon`
<div style="background: #f3e5f5; padding: 15px; border-left: 4px solid #9c27b0; margin: 10px 0;">

**Purpose**: Apply coupon to cart  
**🔒 Authentication**: ❌ No  

**📤 Request Body**:
```json
{
  "code": "SAVE10"
}
```

**📥 Success Response (200)**:
```json
{
  "result": true,
  "message": "Coupon applied successfully",
  "data": {
    "coupon": {
      "code": "SAVE10",
      "type": "percentage",
      "value": 10,
      "discount_amount": 8.00,
      "description": "10% off your order"
    },
    "cart_totals": {
      "subtotal": 80.00,
      "coupon_discount": 8.00,
      "total": 72.00
    }
  }
}
```

</div>

---

## 📦 Orders & Checkout

### 🛒 POST `/v1/checkout`
<div style="background: #fff3e0; padding: 15px; border-left: 4px solid #ff9800; margin: 10px 0;">

**Purpose**: Create order from cart  
**🔒 Authentication**: ❌ No (Supports guest checkout)

**📤 Request Body**:
```json
{
  "cart_id": "cart_12345",
  "guest_email": "customer@example.com",
  "guest_name": "John Doe",
  "guest_phone": "+1234567890",
  "shipping_address": {
    "first_name": "John",
    "last_name": "Doe",
    "company": "ABC Corp",
    "street": "123 Main St",
    "city": "New York",
    "state": "NY",
    "postal_code": "10001",
    "country": "USA",
    "phone": "+1234567890"
  },
  "billing_address": {
    "first_name": "John",
    "last_name": "Doe",
    "street": "123 Main St",
    "city": "New York",
    "state": "NY",
    "postal_code": "10001",
    "country": "USA"
  },
  "payment_method": "stripe",
  "payment_details": {
    "stripe_token": "tok_1234567890"
  },
  "shipping_method": "standard",
  "notes": "Please deliver after 5 PM"
}
```

**📥 Success Response (201)**:
```json
{
  "result": true,
  "message": "Order placed successfully",
  "data": {
    "order": {
      "id": 1001,
      "order_number": "ORD-2024-001001",
      "status": "pending",
      "customer": {
        "email": "customer@example.com",
        "name": "John Doe",
        "phone": "+1234567890"
      },
      "items": [
        {
          "product_id": 1,
          "product_name": "Premium Whey Protein",
          "quantity": 2,
          "unit_price": 39.99,
          "total_price": 79.98
        }
      ],
      "totals": {
        "subtotal": 79.98,
        "tax": 7.20,
        "shipping": 9.99,
        "discount": 5.00,
        "total": 92.17
      },
      "payment": {
        "method": "stripe",
        "status": "pending",
        "amount": 92.17,
        "currency": "USD"
      },
      "shipping_address": {
        "first_name": "John",
        "last_name": "Doe",
        "street": "123 Main St",
        "city": "New York",
        "state": "NY",
        "postal_code": "10001",
        "country": "USA"
      },
      "estimated_delivery": "2024-01-08",
      "created_at": "2024-01-01T15:30:00.000000Z"
    },
    "payment_url": "https://checkout.stripe.com/pay/cs_test_123...",
    "redirect_url": "/order-confirmation/1001"
  }
}
```

</div>

---

## 🩺 BMI Calculator

### 🧮 POST `/v1/bmi`
<div style="background: #e8f5e8; padding: 15px; border-left: 4px solid #4caf50; margin: 10px 0;">

**Purpose**: Create BMI calculation  
**🔒 Authentication**: ❌ No  

**📤 Request Body**:
```json
{
  "gender": "male",
  "age": 30,
  "weight": 80,
  "height": 180,
  "activity": "moderate"
}
```

**📥 Success Response (201)**:
```json
{
  "data": {
    "id": 1,
    "gender": "male",
    "age": 30,
    "weight": 80,
    "height": 180,
    "activity": "moderate",
    "bmi": 24.69,
    "bmi_category": "Normal weight",
    "bmr": 1896.25,
    "tdee": 2610,
    "ideal_weight_range": {
      "min": 65,
      "max": 81
    },
    "body_fat_percentage": {
      "estimated": 15.2,
      "category": "Fitness"
    },
    "recommendations": {
      "calories": {
        "maintain": 2610,
        "lose_weight": 2110,
        "gain_weight": 3110
      },
      "macros": {
        "protein": {
          "grams": 144,
          "calories": 576
        },
        "carbohydrates": {
          "grams": 326,
          "calories": 1305
        },
        "fats": {
          "grams": 87,
          "calories": 783
        }
      },
      "water_intake": "3.5L per day",
      "exercise": "4-5 days per week, 45-60 minutes"
    },
    "health_metrics": {
      "muscle_mass_estimate": "35.2kg",
      "bone_mass_estimate": "3.2kg",
      "metabolic_age": 28
    },
    "created_at": "2024-01-01T12:00:00.000000Z"
  }
}
```

</div>

---

# 🔧 API V2 Endpoints (Admin/CMS)

<div style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); padding: 20px; border-radius: 10px; color: white; margin: 20px 0;">
<h2 style="margin: 0; color: white;">🔐 V2 API - Admin & Management Endpoints</h2>
<p style="margin: 5px 0 0 0; opacity: 0.9;">Administrative API for backend management and CMS operations</p>
</div>

> **⚠️ Important**: All V2 endpoints require authentication unless specified otherwise.

## 📊 Dashboard

### 📈 GET `/v2/dashboard/overview`
<div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;">

**Purpose**: Get dashboard overview statistics  
**🔒 Authentication**: ✅ Yes (Sanctum)

**📥 Success Response (200)**:
```json
{
  "data": {
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
        "order_number": "ORD-2024-001001",
        "customer_name": "John Doe",
        "total": 92.17,
        "status": "processing",
        "created_at": "2024-01-01T15:30:00.000000Z"
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
    ],
    "revenue_chart": {
      "labels": ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
      "datasets": [
        {
          "label": "Revenue",
          "data": [12500, 14200, 13800, 15900, 17200, 16800],
          "backgroundColor": "#4caf50"
        }
      ]
    },
    "alerts": [
      {
        "type": "warning",
        "message": "8 products are low in stock",
        "action_url": "/admin/products?filter=low_stock"
      }
    ]
  }
}
```

</div>

---

## 👥 User Management

### 👤 GET `/v2/users`
<div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;">

**Purpose**: List users (paginated)  
**🔒 Authentication**: ✅ Yes (Sanctum)

**🔍 Query Parameters**:
- `per_page` (optional, default: 15): Items per page
- `role` (optional): Filter by role
- `search` (optional): Search by name or email
- `status` (optional): active, inactive, banned

**📥 Success Response (200)**:
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+1234567890",
      "user_type": "customer",
      "email_verified_at": "2024-01-01T00:00:00.000000Z",
      "status": "active",
      "avatar": "https://example.com/avatars/user1.jpg",
      "roles": ["customer"],
      "permissions": ["view_products", "create_orders"],
      "last_login_at": "2024-01-15T10:30:00.000000Z",
      "orders_count": 5,
      "total_spent": 245.75,
      "address": {
        "street": "123 Main St",
        "city": "New York",
        "country": "USA"
      },
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 147,
    "from": 1,
    "to": 15
  },
  "filters": {
    "roles": ["customer", "admin", "seller"],
    "statuses": ["active", "inactive", "banned"]
  }
}
```

</div>

### ➕ POST `/v2/users`
<div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;">

**Purpose**: Create new user  
**🔒 Authentication**: ✅ Yes (Sanctum)

**📤 Request Body**:
```json
{
  "name": "Jane Smith",
  "email": "jane@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+1987654321",
  "user_type": "customer",
  "roles": ["customer"],
  "address": "456 Oak St",
  "city": "Los Angeles",
  "postal_code": "90210",
  "country": "USA",
  "about_content": "Fitness enthusiast and supplement lover"
}
```

**📥 Success Response (201)**:
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
    "created_at": "2024-01-16T09:00:00.000000Z"
  }
}
```

</div>

---

## 🛍️ Product Management (Admin)

### 📦 GET `/v2/products`
<div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;">

**Purpose**: List products (admin view)  
**🔒 Authentication**: ✅ Yes (Sanctum)

**🔍 Query Parameters**:
- `per_page` (optional, default: 15): Items per page
- `search` (optional): Search term
- `sort_by` (optional, default: "created_at"): Sort field
- `sort_order` (optional, default: "desc"): Sort order
- `type` (optional): Product type filter
- `status` (optional): active, inactive, draft

**📥 Success Response (200)**:
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
      "low_stock_threshold": 10,
      "is_low_stock": false,
      "sales_count": 234,
      "revenue": 9416.00,
      "rating": 4.5,
      "reviews_count": 127,
      "image": "https://example.com/images/protein1.jpg",
      "category": {
        "id": 1,
        "name": "Proteins"
      },
      "brand": {
        "id": 1,
        "name": "DNP Nutrition"
      },
      "translations": {
        "en": {
          "name": "Premium Whey Protein",
          "description": "High-quality whey protein isolate"
        },
        "ar": {
          "name": "بروتين مصل اللبن الممتاز",
          "description": "عزل بروتين مصل اللبن عالي الجودة"
        }
      },
      "seo": {
        "meta_title": "Premium Whey Protein - High Quality",
        "meta_description": "Buy premium whey protein isolate"
      },
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 11,
    "per_page": 15,
    "total": 156
  },
  "filters": {
    "types": ["physical", "digital", "bundle", "package", "session"],
    "statuses": ["active", "inactive", "draft"],
    "categories": [
      {"id": 1, "name": "Proteins"},
      {"id": 2, "name": "Pre-Workout"}
    ]
  }
}
```

</div>

### ➕ POST `/v2/products`
<div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;">

**Purpose**: Create new product  
**🔒 Authentication**: ✅ Yes (Sanctum)

**📤 Request Body**:
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
      "description": "100% pure creatine monohydrate powder",
      "long_description": "Our pure creatine monohydrate is micronized for better absorption..."
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
    },
    {
      "name": "Servings per container",
      "value": "60"
    }
  ],
  "images": [
    {
      "url": "https://example.com/images/creatine1.jpg",
      "alt": "Pure Creatine Monohydrate",
      "is_primary": true
    }
  ],
  "seo": {
    "meta_title": "Pure Creatine Monohydrate - Build Muscle",
    "meta_description": "High-quality creatine supplement for muscle building and performance"
  },
  "weight": 300,
  "dimensions": {
    "length": 12,
    "width": 8,
    "height": 15
  }
}
```

**📥 Success Response (201)**:
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
    "created_at": "2024-01-16T10:00:00.000000Z"
  }
}
```

</div>

---

## 📝 Content Management

### 📄 GET `/v2/posts`
<div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;">

**Purpose**: List posts  
**🔒 Authentication**: ✅ Yes (Sanctum)

**🔍 Query Parameters**:
- `post_type` (optional): Filter by post type slug
- `status` (optional): published, draft
- `category_id` (optional): Filter by category

**📥 Success Response (200)**:
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
        "en": "Discover the amazing benefits of whey protein for your fitness goals",
        "ar": "اكتشف الفوائد المذهلة لبروتين مصل اللبن لأهداف اللياقة البدنية"
      },
      "content": {
        "en": "<p>Whey protein is one of the most popular supplements...</p>",
        "ar": "<p>بروتين مصل اللبن هو واحد من أشهر المكملات...</p>"
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
      "tags": [
        {"id": 1, "name": "protein"},
        {"id": 2, "name": "nutrition"},
        {"id": 3, "name": "fitness"}
      ],
      "seo": {
        "meta_title": "10 Amazing Benefits of Whey Protein for Fitness",
        "meta_description": "Learn about the top 10 benefits of whey protein..."
      },
      "published_at": "2024-01-15T09:00:00.000000Z",
      "created_at": "2024-01-14T15:30:00.000000Z",
      "updated_at": "2024-01-15T09:00:00.000000Z"
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

</div>

### ➕ POST `/v2/posts`
<div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;">

**Purpose**: Create post  
**🔒 Authentication**: ✅ Yes (Sanctum)

**📤 Request Body**:
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
  "seo": {
    "meta_title": "The Science Behind Creatine Supplementation",
    "meta_description": "Learn the scientific facts about creatine supplementation and how it enhances performance."
  },
  "tags": ["creatine", "science", "supplementation"],
  "status": "published",
  "published_at": "2024-01-16T10:00:00.000000Z"
}
```

**📥 Success Response (201)**:
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
    "published_at": "2024-01-16T10:00:00.000000Z",
    "created_at": "2024-01-16T10:00:00.000000Z"
  }
}
```

</div>

---

## 🎨 Media Management

### 📁 GET `/v2/media`
<div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;">

**Purpose**: List media files  
**🔒 Authentication**: ✅ Yes (Sanctum)

**🔍 Query Parameters**:
- `filename` (optional): Filter by filename
- `folder_id` (optional): Filter by folder
- `type` (optional): image, video, document
- `date_from` (optional): Filter by date range
- `date_to` (optional): Filter by date range
- `tags` (optional): Filter by tags

**📥 Success Response (200)**:
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
      "caption": "High-quality whey protein isolate container",
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
        {"id": 2, "name": "protein"},
        {"id": 3, "name": "supplement"}
      ],
      "metadata": {
        "exif": {
          "camera": "Canon EOS R5",
          "iso": "100",
          "aperture": "f/8.0"
        }
      },
      "usage": {
        "used_in": ["products", "blog_posts"],
        "reference_count": 5
      },
      "uploaded_by": {
        "id": 1,
        "name": "Admin User"
      },
      "created_at": "2024-01-15T14:30:00.000000Z",
      "updated_at": "2024-01-15T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 15,
    "per_page": 20,
    "total": 287
  },
  "storage_info": {
    "total_files": 287,
    "total_size": "45.2 MB",
    "available_space": "2.1 GB"
  }
}
```

</div>

### 📤 POST `/v2/media`
<div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;">

**Purpose**: Upload media files  
**🔒 Authentication**: ✅ Yes (Sanctum)

**📤 Request Body** (multipart/form-data):
```json
{
  "files": ["file1.jpg", "file2.png"],
  "folder_id": 1,
  "tags": ["product", "new-arrival"],
  "alt_text": "Product image",
  "caption": "New product showcase"
}
```

**📥 Success Response (201)**:
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

</div>

---

## 📊 Settings Management

### ⚙️ GET `/v2/settings`
<div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;">

**Purpose**: Get all settings  
**🔒 Authentication**: ✅ Yes (Sanctum)

**📥 Success Response (200)**:
```json
{
  "data": {
    "general": {
      "site_name": "DNP Nutrition Store",
      "site_description": "Premium supplements for fitness enthusiasts",
      "site_logo": "https://example.com/logo.png",
      "site_favicon": "https://example.com/favicon.ico",
      "timezone": "America/New_York",
      "date_format": "Y-m-d",
      "time_format": "H:i:s"
    },
    "contact": {
      "email": "info@dnpnutrition.com",
      "phone": "+1-800-DNP-NUTR",
      "address": "123 Fitness St, Health City, HC 12345",
      "social_media": {
        "facebook": "https://facebook.com/dnpnutrition",
        "instagram": "https://instagram.com/dnpnutrition",
        "twitter": "https://twitter.com/dnpnutrition"
      }
    },
    "ecommerce": {
      "currency": "USD",
      "currency_symbol": "$",
      "tax_rate": 8.25,
      "shipping": {
        "free_shipping_threshold": 75.00,
        "standard_shipping_cost": 9.99,
        "express_shipping_cost": 19.99
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
    },
    "localization": {
      "default_language": "en",
      "available_languages": ["en", "ar"],
      "rtl_support": true,
      "auto_translate": false
    },
    "seo": {
      "meta_title": "DNP Nutrition - Premium Supplements",
      "meta_description": "Shop premium supplements, proteins, and nutrition products at DNP Nutrition. Free shipping on orders over $75.",
      "meta_keywords": "supplements, protein, nutrition, fitness, health",
      "google_analytics": "GA_MEASUREMENT_ID",
      "facebook_pixel": "FB_PIXEL_ID"
    },
    "security": {
      "two_factor_auth": true,
      "password_min_length": 8,
      "session_timeout": 120,
      "max_login_attempts": 5
    },
    "maintenance": {
      "maintenance_mode": false,
      "maintenance_message": "We're currently updating our site. Please check back soon!",
      "allowed_ips": ["192.168.1.1", "10.0.0.1"]
    }
  }
}
```

</div>

### 🔧 PATCH `/v2/settings`
<div style="background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;">

**Purpose**: Update single setting  
**🔒 Authentication**: ✅ Yes (Sanctum)

**📤 Request Body**:
```json
{
  "key": "general.site_name",
  "value": {
    "en": "DNP Nutrition Store",
    "ar": "متجر دي إن بي للتغذية"
  }
}
```

**📥 Success Response (200)**:
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
    "updated_at": "2024-01-16T11:00:00.000000Z"
  }
}
```

</div>

---

## 📊 Common Response Formats

### ✅ Success Response
```json
{
  "result": true,
  "message": "Operation completed successfully",
  "data": {
    // Response data here
  }
}
```

### 📄 Paginated Response
```json
{
  "data": [
    // Array of items
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 147,
    "from": 1,
    "to": 15,
    "path": "https://api.example.com/v2/products",
    "prev_page_url": null,
    "next_page_url": "https://api.example.com/v2/products?page=2"
  },
  "links": {
    "first": "https://api.example.com/v2/products?page=1",
    "last": "https://api.example.com/v2/products?page=10",
    "prev": null,
    "next": "https://api.example.com/v2/products?page=2"
  }
}
```

### ❌ Error Response
```json
{
  "result": false,
  "message": "Validation failed",
  "errors": {
    "email": [
      "The email field is required.",
      "The email must be a valid email address."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  },
  "error_code": "VALIDATION_ERROR"
}
```

### 🚫 Authentication Error
```json
{
  "message": "Unauthenticated.",
  "error_code": "UNAUTHENTICATED",
  "status": 401
}
```

### 🔒 Authorization Error
```json
{
  "message": "This action is unauthorized.",
  "error_code": "UNAUTHORIZED",
  "required_permissions": ["manage_products"],
  "status": 403
}
```

---

## 🌟 Key Features

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">

<div style="background: #e8f5e8; padding: 20px; border-radius: 10px; border-left: 4px solid #4caf50;">
<h3 style="color: #2e7d32; margin-top: 0;">🌍 Multi-language Support</h3>
<p>Complete English and Arabic translations for all content types including products, categories, posts, and UI elements.</p>
</div>

<div style="background: #e3f2fd; padding: 20px; border-radius: 10px; border-left: 4px solid #2196f3;">
<h3 style="color: #1565c0; margin-top: 0;">🛍️ Product Types</h3>
<p>Support for physical products, digital downloads, bundles, packages, and consultation sessions.</p>
</div>

<div style="background: #f3e5f5; padding: 20px; border-radius: 10px; border-left: 4px solid #9c27b0;">
<h3 style="color: #7b1fa2; margin-top: 0;">👤 Guest Checkout</h3>
<p>Seamless guest checkout process without requiring user registration, with order tracking via email.</p>
</div>

<div style="background: #fff3e0; padding: 20px; border-radius: 10px; border-left: 4px solid #ff9800;">
<h3 style="color: #f57c00; margin-top: 0;">🎫 Coupon System</h3>
<p>Advanced coupon management with percentage/fixed discounts, usage limits, expiration dates, and user restrictions.</p>
</div>

<div style="background: #e8f5e8; padding: 20px; border-radius: 10px; border-left: 4px solid #4caf50;">
<h3 style="color: #2e7d32; margin-top: 0;">🧮 BMI Calculator</h3>
<p>Built-in BMI and nutrition calculator with personalized recommendations for calories, macros, and exercise.</p>
</div>

<div style="background: #ffebee; padding: 20px; border-radius: 10px; border-left: 4px solid #f44336;">
<h3 style="color: #c62828; margin-top: 0;">📝 CMS System</h3>
<p>Full content management with custom post types, categories, media library, forms, and flexible content blocks.</p>
</div>

</div>

---

## 🔒 Security Considerations

<div style="background: #ffebee; padding: 20px; border-radius: 10px; border: 1px solid #f44336; margin: 20px 0;">

### ⚠️ **Important Security Issues**:

1. **🚨 Critical**: `/v1/auth/password/change` endpoint allows password changes without authentication - **Remove immediately**
2. **🔍 Data Exposure**: Ensure sensitive user data is properly filtered in API responses
3. **✅ Input Validation**: All endpoints validate and sanitize input data using Laravel's form requests
4. **🔐 Rate Limiting**: Implement rate limiting on authentication endpoints to prevent brute force attacks
5. **🛡️ CORS Configuration**: Properly configure CORS settings for production deployment

</div>

---

## 🚀 Migration Guide

Since you've moved away from Blade views to API-first architecture:

### 🎯 For Frontend Teams

#### 1. **Authentication Flow**
```javascript
// Login Example
const login = async (email, password) => {
  const response = await fetch('/api/v1/auth/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ email, password })
  });
  
  const data = await response.json();
  if (data.result) {
    localStorage.setItem('token', data.access_token);
    return data.user;
  }
  throw new Error(data.message);
};
```

#### 2. **API Consumption**
```javascript
// Authenticated Request Example
const fetchProducts = async () => {
  const token = localStorage.getItem('token');
  const response = await fetch('/api/v2/products', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  
  return response.json();
};
```

### 🔧 For Backend Teams

#### 1. **API Development Focus**
- Use V2 API for all admin functionality
- Maintain V1 API for public features  
- Implement proper error handling and validation
- Add comprehensive logging and monitoring

#### 2. **Database Optimization**
- Index frequently queried fields
- Optimize queries for API performance
- Implement proper pagination
- Use database transactions where appropriate

---

## 📈 Performance Recommendations

<div style="background: #e8f5e8; padding: 20px; border-radius: 10px; margin: 20px 0;">

### 🚀 **Optimization Tips**:

1. **📦 API Caching**: Implement Redis caching for frequently accessed data
2. **🔍 Database Indexing**: Index commonly queried fields (email, slug, status)
3. **📄 Pagination**: Always use pagination for list endpoints
4. **🖼️ Image Optimization**: Implement image resizing and WebP conversion
5. **⚡ Response Compression**: Enable Gzip compression for API responses
6. **📊 API Monitoring**: Use tools like New Relic or DataDog for performance monitoring

</div>

---

## 📞 Support & Resources

<div align="center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 15px; color: white; margin: 30px 0;">

### 🆘 Need Help?

**📚 Documentation**: Complete API reference with examples  
**🐛 Bug Reports**: GitHub Issues for bug tracking  
**💡 Feature Requests**: GitHub Discussions for new features  
**📧 Support**: Contact the development team for assistance

### 🛠️ Development Tools

**🧪 API Testing**: Use Postman or Insomnia with provided examples  
**📖 API Documentation**: Interactive docs available at `/docs`  
**🔍 Debugging**: Laravel Telescope for API debugging  
**📊 Monitoring**: Built-in logging and error tracking

</div>

---

<div align="center" style="margin-top: 40px;">

**🎉 The DNP Backend API provides a complete e-commerce and CMS solution tailored for supplement/nutrition businesses with comprehensive multi-language support and advanced product management capabilities.**

![Made with ❤️](https://img.shields.io/badge/Made%20with-❤️-red?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=for-the-badge&logo=laravel)
![API Ready](https://img.shields.io/badge/API-Ready-green?style=for-the-badge)

</div>