<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushCampaign extends Model
{
    protected $fillable = [
        'portal_id',
        'user_id',
        'title',
        'message',
        'target_url',
        'image_url',
        'segment',
        'sent_count',
        'click_count',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
