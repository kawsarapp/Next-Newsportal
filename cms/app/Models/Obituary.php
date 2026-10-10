<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Obituary extends Model
{
    protected $fillable = [
        'portal_id',
        'deceased_name',
        'slug',
        'age',
        'death_date',
        'passed_away_place',
        'bio',
        'funeral_details',
        'photo',
        'family_contact',
        'status',
    ];

    protected $casts = [
        'death_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function ($obituary) {
            if (empty($obituary->slug)) {
                $obituary->slug = Str::slug($obituary->deceased_name) . '-' . Str::random(5);
            }
        });
    }
}
