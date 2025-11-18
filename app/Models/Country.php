<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class Country extends Model
{
    protected $fillable = [
        'code',
        'name',
        'label',
        'zone_id',
        'status',
    ];

    protected $casts = [
        'label' => 'array',
    ];

    /**
     * Get the Zone that owns the Country
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function scopeIsEnabled($query)
    {
        return $query->where('status', '1');
    }

    /**
     * Get translated label based on locale
     *
     * @param string $lang
     * @return string
     */
    public function getTranslatedLabel($lang = null)
    {
        $lang = $lang ?? App::getLocale();

        // If label is JSON array, get the translation
        if (is_array($this->label) && isset($this->label[$lang])) {
            return $this->label[$lang];
        }

        // Fallback to name
        return $this->name;
    }
}
