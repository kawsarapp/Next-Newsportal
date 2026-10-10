<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialAccount extends Model
{
    protected $fillable = ['portal_id','platform','account_name','account_id','access_token','refresh_token','token_expires_at','status','meta'];
    protected $hidden   = ['access_token','refresh_token'];
    protected $casts    = ['token_expires_at'=>'datetime','meta'=>'array'];

    public function portal(): BelongsTo { return $this->belongsTo(Portal::class); }
    public function posts(): HasMany    { return $this->hasMany(SocialPost::class); }
}
