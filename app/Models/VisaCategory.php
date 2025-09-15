<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisaCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
