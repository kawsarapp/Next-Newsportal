<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostViewLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['post_id','portal_id','ip_address','user_agent','referer','device_type','viewed_at'];
    protected $casts    = ['viewed_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (PostViewLog $log) {
            if (empty($log->viewed_at)) {
                $log->viewed_at = now();
            }
        });
    }
}
