<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageRequiredDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id','code','label','required','translation_possible','active'
    ];

    protected $casts = [
        'required' => 'bool',
        'translation_possible' => 'bool',
        'active' => 'bool',
    ];

    public function package(){ return $this->belongsTo(Package::class); }
}
