<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditorialNote extends Model
{
    protected $fillable = ['post_id','user_id','note','is_private'];
    protected $casts    = ['is_private' => 'boolean'];

    public function post(): BelongsTo { return $this->belongsTo(Post::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
