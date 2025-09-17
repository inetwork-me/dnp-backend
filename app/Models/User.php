<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'email_verified_at', 'verification_code', 'about_content', 'remaining_uploads', 'avatar_original', 'avatar', 'device_token', 'user_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function products()
    {
        return $this->hasMany(Product::class);
    }


    public function staff()
    {
        return $this->hasOne(Staff::class);
    }
    public function role()
    {
        return $this->hasOne(Roles::class);
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Helper method to get or create customer profile
    public function getOrCreateCustomer()
    {
        if (!$this->customer) {
            return $this->customer()->create([
                'first_name' => $this->name ? explode(' ', $this->name)[0] : null,
                'last_name' => $this->name && str_contains($this->name, ' ') ? 
                              substr($this->name, strpos($this->name, ' ') + 1) : null,
                'phone' => $this->phone,
                'billing_city' => $this->city,
                'billing_country' => $this->country,
                'billing_postal_code' => $this->postal_code,
                'billing_address' => $this->address,
            ]);
        }
        return $this->customer;
    }
}
