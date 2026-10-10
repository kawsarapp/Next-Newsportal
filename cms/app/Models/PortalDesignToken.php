<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalDesignToken extends Model
{
    protected $fillable = ['portal_id', 'token_key', 'token_value', 'category'];

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }
}
