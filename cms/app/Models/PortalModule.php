<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalModule extends Model
{
    protected $fillable = ['portal_id', 'module', 'enabled', 'config'];

    protected $casts = [
        'enabled' => 'boolean',
        'config'  => 'array',
    ];

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }
}
