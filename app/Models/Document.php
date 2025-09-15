<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $fillable = [
        'application_id','type','original_name','stored_path','size_bytes','mime','status','needs_translation','translation_status','meta'
    ];
    protected $casts = [
        'needs_translation' => 'bool',
        'meta' => 'array',
    ];
    public function application(){ return $this->belongsTo(Application::class); }
}
