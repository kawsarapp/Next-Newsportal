<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    protected $fillable = ['portal_id','reporter_id','assigned_by','post_id','title','brief','priority','deadline','status'];
    protected $casts    = ['deadline' => 'datetime'];

    public function reporter(): BelongsTo   { return $this->belongsTo(User::class, 'reporter_id'); }
    public function assignedBy(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by'); }
    public function post(): BelongsTo       { return $this->belongsTo(Post::class); }
}
