<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalSetting extends Model
{
    protected $fillable = ['portal_id', 'key', 'value', 'type', 'group'];

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }
}
