<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $fillable = ['application_id','attorney_id','type','content','requested_documents'];
    protected $casts = [
        'requested_documents' => 'array',
    ];
    public function application(){ return $this->belongsTo(Application::class); }
    public function attorney(){ return $this->belongsTo(User::class,'attorney_id'); }
}
