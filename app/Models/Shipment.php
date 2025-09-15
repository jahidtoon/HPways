<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'application_id',
        'tracking_number',
        'carrier',
        'service',
        'recipient_name',
        'recipient_address',
        'recipient_city',
        'recipient_state',
        'recipient_zip',
        'recipient_phone',
        'special_instructions',
        'status',
        'prepared_at',
        'shipped_at',
        'delivered_at',
        'estimated_delivery',
        'address',
        'prepared_by',
        'shipped_by'
    ];
    
    protected $casts = [
        'address' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_delivery' => 'date',
        'prepared_at' => 'datetime',
    ];
    
    public function application()
    { 
        return $this->belongsTo(Application::class); 
    }
    
    public function applications()
    {
        return $this->belongsToMany(Application::class, 'application_shipment');
    }
    
    public function trackingEvents()
    { 
        return $this->hasMany(TrackingEvent::class)->orderBy('event_date', 'desc'); 
    }
    
    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function shippedBy()
    {
        return $this->belongsTo(User::class, 'shipped_by');
    }
}
