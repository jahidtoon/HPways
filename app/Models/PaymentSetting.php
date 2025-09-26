<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'is_active',
        'credentials',
        'settings'
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get active payment gateways
     */
    public static function getActiveGateways()
    {
        return self::where('is_active', true)->get();
    }

    /**
     * Get gateway by name
     */
    public static function getGateway(string $gateway)
    {
        return self::where('gateway', $gateway)->first();
    }

    /**
     * Check if gateway is active
     */
    public static function isGatewayActive(string $gateway): bool
    {
        $setting = self::getGateway($gateway);
        return $setting && $setting->is_active;
    }
}
