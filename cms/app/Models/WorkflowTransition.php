<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowTransition extends Model
{
    public const UPDATED_AT = null;
    public const CREATED_AT = 'created_at';

    protected $fillable = ['workflow_id','post_id','from_stage','to_stage','by_user_id','note','ip_address','created_at'];
    protected $casts    = ['created_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (WorkflowTransition $transition) {
            if (empty($transition->created_at)) {
                $transition->created_at = now();
            }
        });
    }

    public function workflow(): BelongsTo { return $this->belongsTo(EditorialWorkflow::class); }
    public function byUser(): BelongsTo  { return $this->belongsTo(User::class, 'by_user_id'); }
}
