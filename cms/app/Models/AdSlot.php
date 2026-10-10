<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdSlot extends Model
{
    protected $fillable = ['portal_id','name','location','device_targets','page_targets','is_active'];
    protected $casts    = ['device_targets'=>'array','page_targets'=>'array','is_active'=>'boolean'];

    public function portal(): BelongsTo     { return $this->belongsTo(Portal::class); }
    public function campaigns(): HasMany    { return $this->hasMany(AdCampaign::class, 'slot_id'); }
}
