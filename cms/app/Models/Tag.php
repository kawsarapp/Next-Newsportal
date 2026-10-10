<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = ['portal_id','name','slug','description','type','post_count'];

    protected static function booted(): void
    {
        static::addGlobalScope(new Scopes\PortalScope());
    }

    public function portal(): BelongsTo  { return $this->belongsTo(Portal::class); }
    public function posts()              { return $this->belongsToMany(Post::class)->withTimestamps(); }
}
