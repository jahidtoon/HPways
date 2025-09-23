<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'name',
    'username',
    'first_name',
    'last_name',
    'email',
    'password',
    'birth_date',
    'is_suspended',
    'is_active',
    ];

    // Applications relationship for applicants (user_id)
    public function applications(){ return $this->hasMany(Application::class); }
    
    // Cases managed by case manager (case_manager_id)
    public function managedCases(){ return $this->hasMany(Application::class, 'case_manager_id'); }
    
    // Cases assigned to attorney (attorney_id)  
    public function assignedCases(){ return $this->hasMany(Application::class, 'attorney_id'); }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'is_suspended' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user's age
     */
    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }
        
        return $this->birth_date->diffInYears(now());
    }
}
