<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Quiz extends Model
{
    protected $fillable = [
        'portal_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'time_limit_minutes',
        'passing_score',
        'status',
        'total_attempts',
    ];

    protected static function booted(): void
    {
        static::creating(function ($quiz) {
            if (empty($quiz->slug)) {
                $quiz->slug = Str::slug($quiz->title) . '-' . Str::random(5);
            }
        });
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
