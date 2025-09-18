<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    // Normalized fields used by selection & payment controllers
    protected $fillable = [
        'visa_type', 'visa_category_id', 'code', 'name', 'price_cents', 'features', 'active'
    ];

    protected $casts = [
        'features' => 'array',
        'active' => 'bool',
    ];

    public function visaCategory()
    {
        return $this->belongsTo(VisaCategory::class, 'visa_category_id');
    }

    // Convenience accessor for float price (UI legacy)
    public function getPriceAttribute()
    {
        return $this->price_cents ? $this->price_cents / 100 : null;
    }

    /**
     * Backward compatibility: if legacy decimal price column exists, expose as accessor in cents.
     */
    public function getPriceCentsAttribute($value)
    {
        if (!is_null($value)) return (int) $value; // already integer cents
        if (array_key_exists('price', $this->attributes)) {
            return (int) round($this->attributes['price'] * 100);
        }
        return null;
    }

    public function setPriceCentsAttribute($value): void
    {
        $this->attributes['price_cents'] = (int) $value;
    }

    public function requiredDocuments()
    {
        return $this->hasMany(\App\Models\PackageRequiredDocument::class);
    }
}
