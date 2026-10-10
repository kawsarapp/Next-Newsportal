<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobCircular extends Model
{
    protected $fillable = [
        'portal_id',
        'title',
        'slug',
        'organization',
        'job_type',
        'vacancy_count',
        'salary_range',
        'location',
        'deadline',
        'application_url',
        'description',
        'circular_image',
        'status',
        'views_count',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = Str::slug($job->title) . '-' . Str::random(5);
            }
        });
    }
}
