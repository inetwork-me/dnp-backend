# DNP Backend API Documentation

## Overview
The DNP Backend is a Laravel-based e-commerce/CMS system focused on supplement/nutrition products. It provides two API versions (V1 and V2) with comprehensive functionality for product management, content management, user management, and e-commerce operations.

**Base URL**: `https://your-domain.com/api/`

## Authentication
The API uses Laravel Sanctum for authentication. Most V2 endpoints require authentication, while V1 has both public and protected endpoints.

**Authentication Header**: `Authorization: Bearer {token}`

---

## API V1 Endpoints

### Authentication & User Management

#### POST `/v1/auth/login`
**Purpose**: User login (email/phone)  
**Authentication**: No  
**Parameters**:
- `email` (required): Email or phone number
- `password` (required): User password
- `login_by` (optional): "email" or "phone"

#### POST `/v1/auth/signup`
**Purpose**: User registration  
**Authentication**: No  
**Parameters**:
- `name` (required): Full name
- `email` (required): Email address
- `password` (required): Password (min 6 characters)
- `password_confirmation` (required): Password confirmation

#### POST `/v1/auth/social-login`
**Purpose**: Social media login  
**Authentication**: No  
**Parameters**:
- `social_provider` (required): "facebook", "google", "twitter", "apple"
- `access_token` (required): Social provider access token
- `provider` (required): Provider ID

#### GET `/v1/auth/logout`
**Purpose**: Logout current user  
**Authentication**: Yes (sanctum)

#### GET `/v1/auth/user`
**Purpose**: Get current user details  
**Authentication**: Yes (sanctum)

#### GET `/v1/auth/account-deletion`
**Purpose**: Delete user account  
**Authentication**: Yes (sanctum)

#### POST `/v1/auth/password/forget_request`
**Purpose**: Request password reset  
**Authentication**: No  
**Parameters**:
- `email` (required): User email

#### POST `/v1/auth/password/confirm_reset`
**Purpose**: Confirm password reset with code  
**Authentication**: No  
**Parameters**:
- `email` (required): User email
- `code` (required): Reset code
- `password` (required): New password

#### POST `/v1/auth/password/change`
**Purpose**: Change password (SECURITY ISSUE - TODO DELETE)  
**Authentication**: No  
**Parameters**:
- `email` (required): User email
- `password` (required): New password

⚠️ **Security Note**: This endpoint allows password changes without authentication and should be removed.

### Products & Categories

#### GET `/v1/products`
**Purpose**: List products with filtering  
**Authentication**: No  
**Query Parameters**:
- `count_per_page` (optional, default: 10): Items per page
- `type` (optional): Product type filter
- `is_top_selling` (optional): Filter top-selling products

#### GET `/v1/products/{slug}`
**Purpose**: Get product details by slug  
**Authentication**: No  
**Response**: Product details with reviews, ratings breakdown

#### GET `/v1/products/brand/{slug}`
**Purpose**: Get products by brand  
**Authentication**: No  
**Query Parameters**:
- `name` (optional): Search filter

#### GET `/v1/categories`
**Purpose**: List categories  
**Authentication**: No  
**Query Parameters**:
- `parent_id` (optional): Filter by parent category

#### GET `/v1/categories/featured`
**Purpose**: Get featured categories  
**Authentication**: No

#### GET `/v1/categories/home`
**Purpose**: Get home page categories  
**Authentication**: No

#### GET `/v1/categories/top`
**Purpose**: Get top categories  
**Authentication**: No

#### GET `/v1/category/info/{slug}`
**Purpose**: Get category info by slug  
**Authentication**: No

#### GET `/v1/sub-categories/{id}`
**Purpose**: Get subcategories  
**Authentication**: No

### Brands

#### GET `/v1/brands`
**Purpose**: List brands  
**Authentication**: No  
**Query Parameters**:
- `name` (optional): Search filter
- `per_page` (optional, default: 10): Items per page

#### POST `/v1/brands`
**Purpose**: Create new brand  
**Authentication**: No  
**Parameters**:
- `logo` (required): Base64 encoded image
- `translations` (required): Brand translations object

#### GET `/v1/brands/top`
**Purpose**: Get top brands  
**Authentication**: No

#### GET `/v1/all-brands`
**Purpose**: Get all brands  
**Authentication**: No

### Shopping Cart

#### GET `/v1/cart`
**Purpose**: Get current cart  
**Authentication**: No  
**Response**: Cart details with items, totals, applied coupons

#### POST `/v1/cart/items`
**Purpose**: Add item to cart  
**Authentication**: No  
**Parameters**:
- `product_id` (required): Product ID
- `quantity` (required): Item quantity
- `options` (optional): Product options

#### PUT `/v1/cart/items/{item}`
**Purpose**: Update cart item  
**Authentication**: No  
**Parameters**:
- `quantity` (optional): New quantity
- `options` (optional): Updated options

#### DELETE `/v1/cart/items/{item}`
**Purpose**: Remove cart item  
**Authentication**: No

#### POST `/v1/cart/{cart}/apply-coupon`
**Purpose**: Apply coupon to cart  
**Authentication**: No  
**Parameters**:
- `code` (required): Coupon code

#### DELETE `/v1/cart/{cart}/remove-coupon`
**Purpose**: Remove coupon from cart  
**Authentication**: No

### Orders

#### GET `/v1/auth/orders`
**Purpose**: Get user orders  
**Authentication**: Yes (sanctum)

#### GET `/v1/auth/orders/{order}`
**Purpose**: Get order details  
**Authentication**: Yes (sanctum)

#### POST `/v1/checkout`
**Purpose**: Create order from cart  
**Authentication**: No (supports guest checkout)  
**Parameters**:
- `cart_id` (required): Cart ID
- `shipping_address` (required): Shipping address object
- `billing_address` (optional): Billing address object
- `payment_method` (optional): Payment method
- `guest_email` (required if not authenticated): Guest email
- `guest_name` (required if not authenticated): Guest name

### Reviews

#### POST `/v1/reviews`
**Purpose**: Create product review  
**Authentication**: Yes (sanctum)  
**Parameters**:
- `product_id` (required): Product ID
- `rating` (required): Rating (1-5)
- `comment` (optional): Review comment

### BMI Calculator

#### GET `/v1/bmi`
**Purpose**: List BMI records  
**Authentication**: No

#### POST `/v1/bmi`
**Purpose**: Create BMI calculation  
**Authentication**: No  
**Parameters**:
- `gender` (required): "male" or "female"
- `age` (required): Age in years
- `weight` (required): Weight in kg
- `height` (required): Height in cm
- `activity` (required): Activity level

#### GET `/v1/bmi/{bmi}`
**Purpose**: Get BMI record  
**Authentication**: No

#### PUT `/v1/bmi/{bmi}`
**Purpose**: Update BMI record  
**Authentication**: No

#### DELETE `/v1/bmi/{bmi}`
**Purpose**: Delete BMI record  
**Authentication**: No

### Website Content

#### GET `/v1/settings`
**Purpose**: Get website settings  
**Authentication**: No

#### GET `/v1/posts/{slug}`
**Purpose**: Get post by slug  
**Authentication**: No

#### GET `/v1/post-types/{postType:slug}/posts`
**Purpose**: Get posts by post type  
**Authentication**: No

#### GET `/v1/post-types/{postType}/categories`
**Purpose**: Get post type categories  
**Authentication**: No

#### GET `/v1/post-types/{postType}`
**Purpose**: Get post type details  
**Authentication**: No

### Forms

#### GET `/v1/forms/slug/{slug}`
**Purpose**: Get form by slug  
**Authentication**: No

#### POST `/v1/forms/slug/{slug}/submit`
**Purpose**: Submit form data  
**Authentication**: No

### Utility Endpoints

#### GET `/v1/languages`
**Purpose**: Get active languages  
**Authentication**: No

#### GET `/v1/banners`
**Purpose**: Get banners  
**Authentication**: No

#### GET `/v1/business-settings`
**Purpose**: Get business settings  
**Authentication**: No

#### GET `/v1/currencies`
**Purpose**: Get active currencies  
**Authentication**: No

#### GET `/v1/get-search-suggestions`
**Purpose**: Get search suggestions  
**Authentication**: No  
**Query Parameters**:
- `query_key` (optional): Search term
- `type` (optional): "product", "brands", "sellers"

#### GET `/v1/filter/categories`
**Purpose**: Get filter categories  
**Authentication**: No

#### GET `/v1/filter/brands`
**Purpose**: Get filter brands  
**Authentication**: No

---

## API V2 Endpoints (Admin/CMS)

All V2 endpoints require authentication unless specified otherwise.

### Authentication

#### POST `/v2/auth/login`
**Purpose**: Admin/user login  
**Authentication**: No

#### POST `/v2/auth/password/forget_request`
**Purpose**: Request password reset  
**Authentication**: No

#### POST `/v2/auth/password/confirm_reset`
**Purpose**: Confirm password reset  
**Authentication**: No

#### POST `/v2/auth/info`
**Purpose**: Get user info by access token  
**Authentication**: No

### Dashboard

#### GET `/v2/dashboard/overview`
**Purpose**: Get dashboard overview statistics  
**Authentication**: Yes (sanctum)

### User Management

#### GET `/v2/users`
**Purpose**: List users (paginated)  
**Authentication**: Yes (sanctum)  
**Query Parameters**:
- `per_page` (optional, default: 15): Items per page
- `role` (optional): Filter by role

#### POST `/v2/users`
**Purpose**: Create new user  
**Authentication**: Yes (sanctum)  
**Parameters**:
- `name` (required): User name
- `email` (required): Email address
- `password` (required): Password (min 8 chars)
- `password_confirmation` (required): Password confirmation
- `roles` (required): Array of role names
- Additional optional fields: address, city, postal_code, phone, country, user_type, about_content

#### GET `/v2/users/{user}`
**Purpose**: Get user details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/users/{user}`
**Purpose**: Update user  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/users/{user}`
**Purpose**: Delete user  
**Authentication**: Yes (sanctum)

### Role Management

#### GET `/v2/roles`
**Purpose**: List all roles  
**Authentication**: Yes (sanctum)

#### POST `/v2/roles`
**Purpose**: Create new role  
**Authentication**: Yes (sanctum)  
**Parameters**:
- `name` (required): Role name
- `guard_name` (optional): Guard name

#### GET `/v2/roles/{role}`
**Purpose**: Get role details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/roles/{role}`
**Purpose**: Update role  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/roles/{role}`
**Purpose**: Delete role  
**Authentication**: Yes (sanctum)

### Product Management

#### GET `/v2/products`
**Purpose**: List products (admin view)  
**Authentication**: Yes (sanctum)  
**Query Parameters**:
- `per_page` (optional, default: 15): Items per page
- `search` (optional): Search term
- `sort_by` (optional, default: "created_at"): Sort field
- `sort_order` (optional, default: "desc"): Sort order
- `type` (optional): Product type filter
- `is_top_selling` (optional): Filter top-selling products

#### POST `/v2/products`
**Purpose**: Create new product  
**Authentication**: Yes (sanctum)  
**Parameters**: Complex product object with translations, categories, stock, taxes, etc.

#### GET `/v2/products/{product}`
**Purpose**: Get product details (admin view)  
**Authentication**: Yes (sanctum)

#### PUT `/v2/products/{product}`
**Purpose**: Update product  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/products/{product}`
**Purpose**: Delete product  
**Authentication**: Yes (sanctum)

#### GET `/v2/products/categories`
**Purpose**: List product categories  
**Authentication**: Yes (sanctum)

#### POST `/v2/products/categories`
**Purpose**: Create product category  
**Authentication**: Yes (sanctum)

#### PUT `/v2/products/categories/{category}`
**Purpose**: Update product category  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/products/categories/{category}`
**Purpose**: Delete product category  
**Authentication**: Yes (sanctum)

### Brand Management

#### GET `/v2/brands`
**Purpose**: List brands (admin view)  
**Authentication**: Yes (sanctum)

#### POST `/v2/brands`
**Purpose**: Create brand  
**Authentication**: Yes (sanctum)

#### GET `/v2/brands/{id}`
**Purpose**: Get brand details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/brands/{id}`
**Purpose**: Update brand  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/brands/{id}`
**Purpose**: Delete brand  
**Authentication**: Yes (sanctum)

### Coupon Management

#### GET `/v2/coupons`
**Purpose**: List coupons  
**Authentication**: Yes (sanctum)

#### POST `/v2/coupons`
**Purpose**: Create coupon  
**Authentication**: Yes (sanctum)

#### GET `/v2/coupons/{coupon}`
**Purpose**: Get coupon details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/coupons/{coupon}`
**Purpose**: Update coupon  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/coupons/{coupon}`
**Purpose**: Delete coupon  
**Authentication**: Yes (sanctum)

### Order Management

#### GET `/v2/orders`
**Purpose**: List orders (admin view)  
**Authentication**: Yes (sanctum)  
**Query Parameters**:
- `per_page` (optional, default: 20): Items per page

#### GET `/v2/orders/{order}`
**Purpose**: Get order details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/orders/{order}/status`
**Purpose**: Update order status  
**Authentication**: Yes (sanctum)  
**Parameters**:
- `status` (required): New status ("pending", "processing", "approved", "shipped", "refunded", "completed", "cancelled")

### Content Management

#### GET `/v2/languages`
**Purpose**: List languages  
**Authentication**: Yes (sanctum)

#### POST `/v2/languages`
**Purpose**: Create language  
**Authentication**: Yes (sanctum)

#### PUT `/v2/languages/{language}`
**Purpose**: Update language  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/languages/{language}`
**Purpose**: Delete language  
**Authentication**: Yes (sanctum)

### Settings Management

#### GET `/v2/settings`
**Purpose**: Get all settings  
**Authentication**: Yes (sanctum)

#### PATCH `/v2/settings`
**Purpose**: Update single setting  
**Authentication**: Yes (sanctum)  
**Parameters**:
- `key` (required): Setting key
- `value` (required): Setting value object

#### PATCH `/v2/settings/batch`
**Purpose**: Update multiple settings  
**Authentication**: Yes (sanctum)  
**Parameters**:
- `settings` (required): Array of setting objects

### Menu Management

#### GET `/v2/menus`
**Purpose**: List menus  
**Authentication**: Yes (sanctum)

#### POST `/v2/menus`
**Purpose**: Create menu  
**Authentication**: Yes (sanctum)

#### GET `/v2/menus/{menu}`
**Purpose**: Get menu details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/menus/{menu}`
**Purpose**: Update menu  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/menus/{menu}`
**Purpose**: Delete menu  
**Authentication**: Yes (sanctum)

#### PATCH `/v2/menus/{menu}/default`
**Purpose**: Set menu as default  
**Authentication**: Yes (sanctum)

### Post Type Management

#### GET `/v2/post-types`
**Purpose**: List post types  
**Authentication**: Yes (sanctum)

#### POST `/v2/post-types`
**Purpose**: Create post type  
**Authentication**: Yes (sanctum)

#### GET `/v2/post-types/{postType}`
**Purpose**: Get post type details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/post-types/{postType}`
**Purpose**: Update post type  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/post-types/{postType}`
**Purpose**: Delete post type  
**Authentication**: Yes (sanctum)

#### GET `/v2/post-types/{postType}/categories`
**Purpose**: List post type categories  
**Authentication**: Yes (sanctum)

#### POST `/v2/post-types/{postType}/categories`
**Purpose**: Create post type category  
**Authentication**: Yes (sanctum)

#### PUT `/v2/post-types/{postType}/categories/{category}`
**Purpose**: Update post type category  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/post-types/{postType}/categories/{category}`
**Purpose**: Delete post type category  
**Authentication**: Yes (sanctum)

### Post Management

#### GET `/v2/posts`
**Purpose**: List posts  
**Authentication**: Yes (sanctum)  
**Query Parameters**:
- `post_type` (optional): Filter by post type slug

#### POST `/v2/posts`
**Purpose**: Create post  
**Authentication**: Yes (sanctum)  
**Parameters**:
- `post_type` (required): Post type slug
- `title` (required): Multilingual title object
- `slug` (required): URL slug
- `category_id` (optional): Category ID
- `description` (optional): Description object
- `content` (optional): Content object
- `blocks` (optional): Blocks array
- `fields` (optional): Custom fields object
- `featured_image` (optional): Featured image object
- `seo` (optional): SEO metadata object
- `status` (optional): "draft" or "published"
- `published_at` (optional): Publication date

#### GET `/v2/posts/{post}`
**Purpose**: Get post details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/posts/{post}`
**Purpose**: Update post  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/posts/{post}`
**Purpose**: Delete post  
**Authentication**: Yes (sanctum)

### Media Management

#### GET `/v2/media`
**Purpose**: List media files  
**Authentication**: Yes (sanctum)  
**Query Parameters**:
- `filename` (optional): Filter by filename
- `folder_id` (optional): Filter by folder
- `date_from` (optional): Filter by date range
- `date_to` (optional): Filter by date range
- `tags` (optional): Filter by tags

#### POST `/v2/media`
**Purpose**: Upload media files  
**Authentication**: Yes (sanctum)  
**Parameters**:
- `files` (required): Array of files to upload
- `folder_id` (optional): Target folder ID
- `tags` (optional): Array of tags

#### GET `/v2/media/{media}`
**Purpose**: Get media details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/media/{media}`
**Purpose**: Update media metadata  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/media/{media}`
**Purpose**: Delete media file  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/media/bulk`
**Purpose**: Bulk delete media files  
**Authentication**: Yes (sanctum)  
**Parameters**:
- `ids` (required): Array of media IDs

### Folder Management

#### GET `/v2/folders`
**Purpose**: List media folders  
**Authentication**: Yes (sanctum)

#### POST `/v2/folders`
**Purpose**: Create folder  
**Authentication**: Yes (sanctum)

#### GET `/v2/folders/{folder}`
**Purpose**: Get folder details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/folders/{folder}`
**Purpose**: Update folder  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/folders/{folder}`
**Purpose**: Delete folder  
**Authentication**: Yes (sanctum)

#### POST `/v2/folders/reorder`
**Purpose**: Reorder folders  
**Authentication**: Yes (sanctum)

### Tag Management

#### GET `/v2/tags`
**Purpose**: List tags  
**Authentication**: Yes (sanctum)

#### POST `/v2/tags`
**Purpose**: Create tag  
**Authentication**: Yes (sanctum)

#### GET `/v2/tags/{tag}`
**Purpose**: Get tag details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/tags/{tag}`
**Purpose**: Update tag  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/tags/{tag}`
**Purpose**: Delete tag  
**Authentication**: Yes (sanctum)

### Block Management

#### GET `/v2/blocks`
**Purpose**: List content blocks  
**Authentication**: Yes (sanctum)

#### POST `/v2/blocks`
**Purpose**: Create block  
**Authentication**: Yes (sanctum)

#### GET `/v2/blocks/{block}`
**Purpose**: Get block details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/blocks/{block}`
**Purpose**: Update block  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/blocks/{block}`
**Purpose**: Delete block  
**Authentication**: Yes (sanctum)

### Form Management

#### GET `/v2/forms`
**Purpose**: List forms  
**Authentication**: Yes (sanctum)

#### POST `/v2/forms`
**Purpose**: Create form  
**Authentication**: Yes (sanctum)

#### GET `/v2/forms/{form}`
**Purpose**: Get form details  
**Authentication**: Yes (sanctum)

#### PUT `/v2/forms/{form}`
**Purpose**: Update form  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/forms/{form}`
**Purpose**: Delete form  
**Authentication**: Yes (sanctum)

#### POST `/v2/forms/{form}/fields`
**Purpose**: Add form field  
**Authentication**: Yes (sanctum)

#### PUT `/v2/forms/{form}/fields/{field}`
**Purpose**: Update form field  
**Authentication**: Yes (sanctum)

#### DELETE `/v2/forms/{form}/fields/{field}`
**Purpose**: Delete form field  
**Authentication**: Yes (sanctum)

#### GET `/v2/forms/{form}/submissions`
**Purpose**: List form submissions  
**Authentication**: Yes (sanctum)

---

## Web Routes (Blade Views)

### Admin Routes
- `GET /admin/dashboard` - Admin dashboard
- Various admin management routes for products, categories, brands, etc.

### Authentication Routes
- `GET /login` - Login page
- `GET /register` - Registration page
- `POST /logout` - Logout action

### File Upload Routes
- `GET /aiz-uploader` - File uploader interface
- `POST /aiz-uploader/upload` - File upload handler

---

## Common Response Formats

### Success Response
```json
{
    "data": {...},
    "message": "Success message"
}
```

### Paginated Response
```json
{
    "data": [...],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

### Error Response
```json
{
    "result": false,
    "message": "Error message",
    "errors": [...]
}
```

## Key Features

1. **Multi-language Support**: Most content supports English and Arabic translations
2. **Product Types**: Supports physical, digital, bundle, package, and session products
3. **Guest Checkout**: Allows guest orders without registration
4. **Coupon System**: Comprehensive coupon management with usage limits
5. **BMI Calculator**: Built-in BMI and nutrition calculator
6. **CMS System**: Full content management with custom post types
7. **Media Management**: Advanced media library with folders and tags
8. **Role-based Access**: Comprehensive user role management
9. **Review System**: Product reviews with ratings
10. **Cart Management**: Persistent shopping cart with coupon support

## Security Considerations

⚠️ **Important Security Issues**:
1. **Password Change Endpoint**: `/v1/auth/password/change` allows password changes without authentication - should be removed immediately
2. **Guest Data Access**: Some endpoints may expose sensitive data without proper authentication
3. **Input Validation**: Ensure all endpoints properly validate and sanitize input data

## Migration Notes

Since you mentioned moving away from Blade views:
1. Most admin functionality should migrate to V2 API endpoints
2. Frontend should consume V1 API endpoints for public functionality
3. Authentication should use the Sanctum token system
4. File uploads should use the media management API endpoints

This API provides a complete e-commerce and CMS solution tailored for supplement/nutrition businesses with comprehensive multi-language support and advanced product management capabilities.