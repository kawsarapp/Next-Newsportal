<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    protected $fillable = [
        'portal_id',
        'endpoint',
        'public_key',
        'auth_token',
        'device_type',
        'browser',
        'ip_address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
