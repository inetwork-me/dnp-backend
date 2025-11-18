<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pickup extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'pickup_guid',
        'reference_number',
        'status',
        'pickup_address_line1',
        'pickup_address_line2',
        'pickup_city',
        'pickup_country',
        'pickup_postal_code',
        'contact_person',
        'contact_company',
        'contact_phone',
        'contact_email',
        'pickup_date',
        'ready_time',
        'last_pickup_time',
        'closing_time',
        'pickup_location',
        'number_of_shipments',
        'total_weight',
        'comments',
        'carrier_response',
        'scheduled_at',
        'completed_at',
        'cancelled_at'
    ];

    protected $casts = [
        'carrier_response' => 'array',
        'pickup_date' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'total_weight' => 'decimal:2'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('pickup_date', '>=', now())
                    ->whereIn('status', [self::STATUS_PENDING, self::STATUS_SCHEDULED]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_SCHEDULED]);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
