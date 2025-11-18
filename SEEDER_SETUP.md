# Database Seeder Setup - Production Safe

## Overview
This project's seeders have been configured to be **PRODUCTION SAFE**. They won't overwrite existing data.

## Initial Setup (Fresh Install)

Run this command for a fresh installation:

```bash
cd dnp-backend
php artisan migrate --seed
```

This will:
1. ✅ Create all database tables
2. ✅ Seed initial data (settings, admin user, etc.)
3. ✅ Give User ID 1 full Super Admin permissions
4. ✅ Set up default configurations

## What Gets Seeded

### 1. **AdminSeeder**
- Creates initial admin user(s)

### 2. **ProductSpecificationsTableSeeder**
- Seeds product specifications

### 3. **SettingTableSeeder** ✅ SAFE
- Creates default site settings (logo, title, contact info, social media)
- **Uses `firstOrCreate`** - Won't overwrite existing settings

### 4. **MenuTableSeeder**
- Seeds menu structure

### 5. **PostTypeSeeder & PostSeeder**
- Creates post types and sample posts

### 6. **PostTypeCategorySeeder**
- Seeds post type categories

### 7. **MediaFolderSeeder & TagSeeder**
- Creates media folders and tags

### 8. **SuperAdminPermissionsSeeder** ✅ NEW & SAFE
- Gives User ID 1 all permissions (Super Admin)
- Creates "Super Admin" role with all permissions
- **Safe to run multiple times** - Won't duplicate or overwrite

### 9. **EmailNotificationSettingsSeeder** ✅ SAFE
- Seeds email notification settings
- **Uses `firstOrCreate`** - Won't overwrite existing settings

### 10. **LoyaltySettingsSeeder** ✅ SAFE
- Seeds loyalty program settings
- **Uses `firstOrCreate`** - Won't overwrite existing settings

## Production Environment

### ⚠️ DON'T RUN THIS IN PRODUCTION:
```bash
php artisan migrate --seed  # ❌ Can cause issues
```

### ✅ Safe Production Commands:

#### Option 1: Run ALL seeders (if needed)
```bash
php artisan db:seed
```

#### Option 2: Run specific seeder only
```bash
# Give User ID 1 all permissions
php artisan db:seed --class=SuperAdminPermissionsSeeder

# Seed settings only
php artisan db:seed --class=SettingTableSeeder
```

## Seeder Safety Features

All settings seeders now use `firstOrCreate` instead of `updateOrCreate`:

| Method | Behavior | Production Safe? |
|--------|----------|------------------|
| `updateOrCreate` | Creates OR updates existing | ❌ Overwrites data |
| `firstOrCreate` | Creates only if doesn't exist | ✅ Preserves existing data |

### Before (Unsafe):
```php
Setting::updateOrCreate(
    ['key' => 'site_title'],
    ['value' => 'Default Title']  // Would overwrite!
);
```

### After (Safe):
```php
Setting::firstOrCreate(
    ['key' => 'site_title'],
    ['value' => 'Default Title']  // Only creates if doesn't exist
);
```

## Super Admin Setup

The **SuperAdminPermissionsSeeder** automatically runs as part of the seed process and:

1. ✅ Finds User ID 1
2. ✅ Creates "Super Admin" role (if doesn't exist)
3. ✅ Gets all permissions from database
4. ✅ Assigns all permissions to Super Admin role
5. ✅ Assigns Super Admin role to User ID 1

### Manual Run (if needed):
```bash
php artisan db:seed --class=SuperAdminPermissionsSeeder
```

## Team Workflow

### Developer 1 (Fresh Setup):
```bash
git clone <repo>
cd dnp-backend
cp .env.example .env
# Configure .env database settings
php artisan key:generate
php artisan migrate --seed
```

### Developer 2 (Fresh Setup):
```bash
git clone <repo>
cd dnp-backend
cp .env.example .env
# Configure .env database settings
php artisan key:generate
php artisan migrate --seed
```

Both developers will get:
- ✅ Same database structure
- ✅ Same initial data
- ✅ User ID 1 with Super Admin permissions
- ✅ All default settings

## Troubleshooting

### Issue: User ID 1 doesn't have permissions
**Solution:**
```bash
php artisan db:seed --class=SuperAdminPermissionsSeeder
```

### Issue: Settings were overwritten
**Solution:**
- This shouldn't happen anymore (fixed with `firstOrCreate`)
- Restore from backup if needed
- All new seeders are safe

### Issue: Duplicate roles/permissions
**Solution:**
```bash
# Check in tinker
php artisan tinker
>>> \Spatie\Permission\Models\Role::where('name', 'Super Admin')->count();
>>> \App\Models\User::find(1)->roles;
```

## Summary

✅ All seeders are now **production safe**
✅ User ID 1 automatically gets Super Admin permissions
✅ Settings won't be overwritten
✅ Safe to run `migrate --seed` for fresh installs
✅ Both teams get consistent setup
