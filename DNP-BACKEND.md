# DNP-BACKEND API Documentation

## Overview

DNP-BACKEND is a Laravel 10.x API-first e-commerce and CMS platform for nutrition/supplement products. It provides two API versions: V1 (customer-facing) and V2 (admin dashboard).

---

## Tech Stack

| Category | Technology |
|----------|------------|
| Framework | Laravel 10.x |
| PHP Version | 8.1+ |
| Authentication | Laravel Sanctum (Bearer tokens) |
| Database | MySQL (primary) |
| ORM | Eloquent |
| Permissions | Spatie Permission v5.5 |
| Image Processing | Intervention/Image v2.5 |
| Excel Export | Maatwebsite/Excel v3.1 |
| PDF Generation | MPDF v8.1 |
| Caching | Redis (Predis) |
| File Storage | Local + AWS S3 |

### Payment Gateways
- Paystack, PayPal, Stripe
- Mercado Pago, MyFatoorah
- Flutterwave, Payku, Rave

---

## Project Structure

```
dnp-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── V1/           # Customer API
│   │   │   │   └── V2/           # Admin API
│   │   │   │       └── Admin/    # Admin-specific
│   │   │   └── [Web controllers]
│   │   └── Middleware/
│   │
│   ├── Models/                   # Eloquent models
│   ├── Services/                 # Business logic
│   ├── Jobs/                     # Queue jobs
│   ├── Notifications/            # Email notifications
│   └── Observers/                # Model observers
│
├── routes/
│   ├── api.php                   # API routes (V1 & V2)
│   └── web.php                   # Web routes
│
├── config/                       # Configuration files
├── database/
│   ├── migrations/               # Database schema
│   └── seeders/                  # Data seeders
│
└── storage/                      # File storage
```

---

## Database Models

### Core Models

| Model | Purpose |
|-------|---------|
| **User** | Authentication & user accounts |
| **Customer** | Customer profile (1:1 with User) |
| **Product** | Products with specs, pricing |
| **Category** | Hierarchical product categories |
| **Brand** | Product brands |
| **Cart** / **CartItem** | Shopping cart |
| **Order** / **OrderItem** | Customer orders |
| **Coupon** | Discount coupons |
| **Review** | Product reviews |

### Shipping Models

| Model | Purpose |
|-------|---------|
| **Shipment** | Shipment tracking |
| **ShippingCarrier** | Carriers (Aramex, DHL, FedEx) |
| **ShippingMethod** | Shipping methods |
| **ShippingZone** | Geographic zones |
| **ShippingQuote** | Rate quotes |

### Loyalty Models

| Model | Purpose |
|-------|---------|
| **LoyaltyPointsTransaction** | Point history |
| **Voucher** | Loyalty vouchers |
| **LoyaltySetting** | Program settings |

### CMS Models

| Model | Purpose |
|-------|---------|
| **Post** / **PostType** | Blog/CMS content |
| **Form** / **FormField** | Dynamic forms |
| **FormSubmission** | Form submissions |
| **Block** | CMS blocks |
| **Media** / **MediaFolder** | Media library |
| **Language** | Multi-language support |

---

## API Endpoints

### V1 - Customer API (`/api/v1`)

#### Authentication
```
POST   /auth/login              # Login
POST   /auth/signup             # Register
POST   /auth/social-login       # Social auth (Google, Apple)
GET    /auth/logout             # Logout (auth required)
GET    /auth/user               # Get current user
POST   /auth/password/forget_request   # Password reset
POST   /auth/password/confirm_reset    # Confirm reset
```

#### Products
```
GET    /products                # List products
GET    /products/{id}           # Product details
GET    /products/random         # Random products
GET    /products/{id}/related   # Related products
GET    /products/brand/{slug}   # Products by brand
GET    /brands                  # List brands
```

#### Categories
```
GET    /categories              # All categories
GET    /categories/featured     # Featured categories
GET    /category/info/{slug}    # Category details
GET    /sub-categories/{id}     # Subcategories
```

#### Cart & Checkout
```
GET    /cart                    # Get cart
POST   /cart/items              # Add to cart
PUT    /cart/items/{item}       # Update item
DELETE /cart/items/{item}       # Remove item
POST   /cart/{cart}/apply-coupon    # Apply coupon
DELETE /cart/{cart}/remove-coupon   # Remove coupon
POST   /checkout                # Create order
```

#### Orders
```
GET    /orders                  # User's orders (auth)
GET    /orders/{order}          # Order details (auth)
```

#### Shipping
```
POST   /shipping/calculate-rates     # Calculate rates
GET    /shipping/quotes/{cartId}     # Get quote
POST   /shipping/quotes/{id}/select-method   # Select method
POST   /shipping/validate-address    # Validate address
GET    /shipments/track/{tracking}   # Track shipment
```

#### Loyalty
```
GET    /loyalty/summary              # Balance (auth)
GET    /loyalty/transactions         # History (auth)
POST   /loyalty/convert-to-voucher   # Convert points
GET    /vouchers                     # User vouchers (auth)
POST   /vouchers/validate            # Validate voucher
```

#### CMS
```
GET    /posts/{slug}                      # Post by slug
GET    /post-types/{type}/posts           # Posts by type
GET    /forms/slug/{slug}                 # Get form
POST   /forms/slug/{slug}/submit          # Submit form
GET    /settings                          # Website settings
```

---

### V2 - Admin API (`/api/v2`)

#### Dashboard
```
GET    /dashboard/overview      # All statistics
```

#### Products Management
```
GET    /products                # List (admin)
POST   /products                # Create
GET    /products/{id}           # Get
PUT    /products/{id}           # Update
DELETE /products/{id}           # Delete
```

#### Categories
```
GET    /products/categories     # List
POST   /products/categories     # Create
PUT    /products/categories/{id}    # Update
DELETE /products/categories/{id}    # Delete
```

#### Coupons
```
GET    /coupons                 # List
POST   /coupons                 # Create
PUT    /coupons/{id}            # Update
DELETE /coupons/{id}            # Delete
```

#### Orders
```
GET    /orders                  # List all
GET    /orders/{id}             # Details
PUT    /orders/{id}/status      # Update status
```

#### Users & Roles
```
GET    /users                   # List users
POST   /users                   # Create
PUT    /users/{id}              # Update
DELETE /users/{id}              # Delete
GET    /roles                   # List roles
POST   /roles                   # Create role
```

#### CMS Management
```
GET    /posts                   # List posts
POST   /posts                   # Create
PUT    /posts/{id}              # Update
DELETE /posts/{id}              # Delete
GET    /forms                   # List forms
POST   /forms                   # Create form
GET    /media                   # List media
POST   /media                   # Upload
DELETE /media/{id}              # Delete
```

#### Shipping Admin
```
GET    /admin/shipping/carriers         # List carriers
POST   /admin/shipping/carriers         # Create
PUT    /admin/shipping/carriers/{id}    # Update
POST   /admin/shipping/carriers/{id}/test-connection  # Test API
```

#### Loyalty Admin
```
GET    /loyalty/dashboard-stats         # Admin stats
POST   /loyalty/manual-adjustment       # Adjust points
GET    /loyalty/customers               # List customers
POST   /loyalty/settings                # Update settings
```

---

## Authentication

### Flow
1. User submits credentials to `/v1/auth/login`
2. Backend validates and returns `access_token`
3. Client stores token and sends as `Authorization: Bearer {token}`
4. Sanctum middleware validates token on protected routes

### Social Login
- Google, Apple, Facebook via Laravel Socialite
- Endpoint: `/v1/auth/social-login`

### Role-Based Access
- Uses Spatie Permission
- Roles: admin, customer, seller
- Middleware: `IsAdmin`, `IsCustomer`, `IsSeller`

---

## Middleware

| Middleware | Purpose |
|------------|---------|
| `AppLanguage` | Set locale from `App-Language` header |
| `Authenticate` | Require authentication |
| `IsAdmin` | Require admin role |
| `IsCustomer` | Require customer role |
| `IsUnbanned` | Check user not banned |
| `HttpsProtocol` | Force HTTPS |

---

## Services

| Service | Responsibility |
|---------|----------------|
| `LoyaltyService` | Points processing, vouchers, tiers |
| `ProductService` | Product business logic |
| `ProductStockService` | Stock management |
| `DashboardService` | Dashboard statistics |
| `DefaultShippingService` | Shipping calculations |

---

## Multi-Language Support

### Implementation
- Languages stored in `languages` table
- Translation tables for each content type:
  - `product_translations`
  - `category_translations`
  - `brand_translations`
  - etc.

### Usage
- `App-Language` header sets locale
- Models use `getTranslation()` helper
- RTL support for Arabic

---

## Rate Limiting

- **Limit:** 600 requests/minute per user/IP
- Configured in `RouteServiceProvider`

---

## Environment Variables

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=libomed
DB_USERNAME=root
DB_PASSWORD=

# Authentication
SANCTUM_STATEFUL_DOMAINS=localhost

# Storage
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=

# Mail
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=

# Payment Gateways
PAYSTACK_PUBLIC_KEY=
PAYSTACK_SECRET_KEY=
MYFATOORAH_API_KEY=
FLUTTERWAVE_PUBLIC_KEY=
```

---

## Key Features

### E-Commerce
- Product catalog with categories, brands
- Stock tracking
- Multi-currency pricing
- Flash deals
- Frequently bought together

### Loyalty Program
- Point earning on purchases
- Voucher redemption
- Membership tiers
- Referral system
- Signup bonuses

### Shipping
- Multiple carriers (Aramex, DHL, FedEx)
- Live rate calculations
- Tracking numbers
- Shipping labels
- Zone-based pricing

### CMS
- Dynamic post types
- Form builder
- Media library
- Multi-language content
- SEO metadata

### Reviews
- Product ratings
- Admin approval workflow
- Review moderation

---

## Development

### Commands

```bash
# Install dependencies
composer install

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Start development server
php artisan serve

# Run tests
php artisan test

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Docker (Laravel Sail)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

---

## Relationship to Other Projects

| Project | Role | Consumes |
|---------|------|----------|
| **libomed-next** | Customer frontend | API V1 |
| **libomed-portal** | Admin dashboard | API V2 |
| **dnp-backend** | API server (this) | - |

---

## API Documentation

Detailed endpoint documentation available in `API_DOCUMENTATION.md`

---

## Security Notes

- CSRF protection enabled
- HTTPS enforcement available
- Rate limiting configured
- User banning system
- OTP-based verification
- Password hashing (bcrypt)
