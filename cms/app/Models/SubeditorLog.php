<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubeditorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'portal_id',
        'post_id',
        'title',
        'category_name',
        'status',
        'url',
        'error_message',
        'ip_address',
    ];

    protected $appends = [
        'category',
        'article_title',
    ];

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function getCategoryAttribute(): ?string
    {
        return $this->category_name;
    }

    public function getArticleTitleAttribute(): ?string
    {
        return $this->title;
    }
}
