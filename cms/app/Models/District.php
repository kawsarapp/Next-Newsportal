<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $table = 'districts';

    protected $fillable = [
        'division_id',
        'name',
        'name_en',
        'bn_name',
        'bn_code',
    ];

    protected $appends = [
        'bn_name',
    ];

    public function getBnNameAttribute(): string
    {
        return $this->attributes['bn_name'] ?? $this->attributes['name'] ?? '';
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'district_id');
    }
}
