<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    public const UPDATED_AT = null;
    public const CREATED_AT = 'created_at';

    protected $table    = 'login_histories';
    protected $fillable = ['user_id','ip_address','user_agent','device_name','status','failure_reason','created_at'];
    protected $casts    = ['created_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (LoginHistory $history) {
            if (empty($history->created_at)) {
                $history->created_at = now();
            }
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
