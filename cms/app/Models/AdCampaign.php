<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdCampaign extends Model
{
    protected $fillable = ['portal_id','advertiser_id','slot_id','name','type','code','start_date','end_date','amount','status','performance'];
    protected $casts    = ['start_date'=>'date','end_date'=>'date','performance'=>'array','amount'=>'decimal:2'];

    public function slot(): BelongsTo       { return $this->belongsTo(AdSlot::class, 'slot_id'); }
    public function advertiser(): BelongsTo { return $this->belongsTo(Advertiser::class); }
}
