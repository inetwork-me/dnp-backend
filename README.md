# 🛒 Nutrition E-commerce Platform

A multi-vendor nutrition-based e-commerce platform built with Laravel. This system supports product and service sales, multi-language and multi-currency functionality, and offers three distinct dashboards for different user roles: **User**, **Vendor**, and **Super Admin**.

## 📦 Features

- 🛍️ Multi-vendor support for product and service listings
- 🌐 Multi-language and multi-currency support
- 🔐 Role-based dashboards
  - **User Dashboard:** Browse, purchase, and manage orders
  - **Vendor Dashboard:** Manage products/services, view sales, and handle orders
  - **Super Admin Dashboard:** Full platform management and analytics
- 🔗 API integration for mobile and third-party services
- 🧾 Secure authentication and authorization
- 💳 Payment gateway integration
- 📈 Real-time reports and analytics
- 🧠 Advanced search and filters

## 🗂️ Project Structure

- `app/`: Laravel application code
- `routes/`: Route definitions for web and API
- `resources/`: Views and language files
- `database/`: Migrations, seeders, and factories
- `public/`: Public assets
- `config/`: Configuration files

## 🚀 Installation

```bash
# Clone the repository
git clone https://github.com/inetwork-me/dnp-backend

# Navigate to the project
cd nutrition-ecommerce

# Install dependencies
composer install

# Copy .env and generate key
cp .env.example .env
php artisan key:generate

# Configure your .env file
# (Database, Mail, Payment Gateway, etc.)

# Run migrations and seeders
php artisan migrate --seed

# Serve the application
php artisan serve
