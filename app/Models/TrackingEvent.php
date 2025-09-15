<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'shipment_id',
        'event_type',
        'description',
        'location',
        'status_class',
        'event_time',
        'event_date'
    ];
    
    protected $casts = [
        'event_time' => 'datetime',
        'event_date' => 'datetime',
    ];
    
    public function shipment()
    { 
        return $this->belongsTo(Shipment::class); 
    }
}
