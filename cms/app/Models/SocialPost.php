<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPost extends Model
{
    protected $fillable = ['portal_id','post_id','social_account_id','platform','caption','status','scheduled_at','published_at','platform_post_id','error_message','performance','retry_count'];
    protected $casts    = ['scheduled_at'=>'datetime','published_at'=>'datetime','performance'=>'array'];

    public function post(): BelongsTo          { return $this->belongsTo(Post::class); }
    public function socialAccount(): BelongsTo { return $this->belongsTo(SocialAccount::class); }
}
