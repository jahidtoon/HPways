<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizNode extends Model
{
    use HasFactory;

    protected $fillable = ['node_id', 'title', 'question', 'type', 'options', 'x', 'y'];

    protected $casts = [
        'options' => 'array',
    ];
}
