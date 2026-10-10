<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EditorialWorkflow extends Model
{
    protected $fillable = [
        'portal_id','post_id','current_stage','assigned_to','assigned_by',
        'deadline','is_embargoed','embargo_until','is_sensitive',
        'requires_legal','requires_fact_check',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->portal_id)) {
                $model->portal_id = auth()->user()?->getEffectivePortalId()
                    ?? (app()->bound('current.portal') ? app('current.portal')?->id : null)
                    ?? ($model->post?->portal_id)
                    ?? (Portal::first()?->id ?? 1);
            }
        });
    }


    protected $casts = [
        'deadline'             => 'datetime',
        'embargo_until'        => 'datetime',
        'is_embargoed'         => 'boolean',
        'is_sensitive'         => 'boolean',
        'requires_legal'       => 'boolean',
        'requires_fact_check'  => 'boolean',
    ];

    public function post(): BelongsTo       { return $this->belongsTo(Post::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function assignedBy(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by'); }
    public function transitions(): HasMany  { return $this->hasMany(WorkflowTransition::class, 'workflow_id')->latest(); }
}
