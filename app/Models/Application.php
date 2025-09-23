<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','visa_type','selected_package_id','status','progress_pct','payment_status','submitted_at','missing_documents','intake_history','case_manager_id','attorney_id','assigned_printer_id','printing_started_at','printed_at'
    ];

    protected $casts = [
        'missing_documents' => 'array',
        'intake_history' => 'array',
        'submitted_at' => 'datetime',
        'printing_started_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    public function user(){ return $this->belongsTo(User::class); }
    public function selectedPackage(){ return $this->belongsTo(Package::class,'selected_package_id'); }
    public function caseManager(){ return $this->belongsTo(User::class,'case_manager_id'); }
    public function attorney(){ return $this->belongsTo(User::class,'attorney_id'); }
    public function assignedPrinter(){ return $this->belongsTo(User::class,'assigned_printer_id'); }
    public function feedback(){ return $this->hasMany(Feedback::class); }
    public function shipment(){ return $this->hasOne(Shipment::class); }
    public function shipments(){ return $this->belongsToMany(Shipment::class, 'application_shipment'); }
    public function payments(){ return $this->hasMany(Payment::class); }
    public function documents(){ return $this->hasMany(Document::class); }
    public function trackingEvents(){ return $this->hasMany(TrackingEvent::class); }
}
