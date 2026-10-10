<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomepageSection extends Model
{
    protected $fillable = [
        'portal_id',
        'category_id',
        'title',
        'section_type',
        'layout_type',
        'posts_count',
        'order',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'posts_count' => 'integer',
        'order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
