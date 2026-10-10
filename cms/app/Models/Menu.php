<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\PortalScope;

class Menu extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = ['portal_id', 'name', 'location', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::addGlobalScope(new PortalScope());
    }

    public function portal(): BelongsTo  { return $this->belongsTo(Portal::class); }
    public function items(): HasMany     { return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('order'); }
    public function allItems(): HasMany  { return $this->hasMany(MenuItem::class)->orderBy('order'); }
}
