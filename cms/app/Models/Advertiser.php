<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Advertiser extends Model
{
    use SoftDeletes;
    protected $fillable = ['portal_id','company_name','contact_person','email','phone','address','notes'];

    public function portal(): BelongsTo    { return $this->belongsTo(Portal::class); }
    public function campaigns(): HasMany   { return $this->hasMany(AdCampaign::class); }
}
