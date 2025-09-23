<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'shipment_id',
        'application_id',
        'user_id',
        'event_type',
        'description',
        'location',
        'status_class',
        'event_time',
        'event_date',
        'occurred_at',
        'metadata'
    ];
    
    protected $casts = [
        'event_time' => 'datetime',
        'event_date' => 'datetime',
        'occurred_at' => 'datetime',
        'metadata' => 'array'
    ];
    
    public function shipment()
    { 
        return $this->belongsTo(Shipment::class); 
    }
    
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
