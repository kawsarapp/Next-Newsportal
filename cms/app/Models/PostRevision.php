<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostRevision extends Model
{
    public const UPDATED_AT = null;
    public const CREATED_AT = 'created_at';

    protected $fillable = [
        'post_id',
        'editor_id',
        'old_content',
        'new_content',
        'change_summary',
        'status_change',
        'editor_ip',
        'created_at',
    ];

    protected $casts = [
        'old_content' => 'array',
        'new_content' => 'array',
        'created_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (PostRevision $revision) {
            if (empty($revision->created_at)) {
                $revision->created_at = now();
            }
        });
    }

    public function post(): BelongsTo   { return $this->belongsTo(Post::class); }
    public function editor(): BelongsTo { return $this->belongsTo(User::class, 'editor_id'); }
}
