<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequiredDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'visa_type','code','label','required','translation_possible','active'
    ];

    protected $casts = [
        'required' => 'bool',
        'translation_possible' => 'bool',
        'active' => 'bool',
    ];
}
