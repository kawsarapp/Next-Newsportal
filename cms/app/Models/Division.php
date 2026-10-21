<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    protected $table = 'divisions';

    protected $fillable = [
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

    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'division_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'division_id');
    }
}
