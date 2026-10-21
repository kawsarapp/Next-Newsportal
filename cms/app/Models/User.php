<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'portal_id', 'name', 'username', 'email', 'password', 'phone', 'bio', 'avatar',
        'reporter_type', 'district_id', 'institution',
        'two_factor_enabled', 'status', 'login_attempts', 'locked_until', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = [
        'password', 'remember_token',
        'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at'         => 'datetime',
        'two_factor_confirmed_at'   => 'datetime',
        'two_factor_enabled'        => 'boolean',
        'locked_until'              => 'datetime',
        'last_login_at'             => 'datetime',
        'password'                  => 'hashed',
    ];

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar;
    }

    public function setAvatarUrlAttribute(?string $value): void
    {
        $this->attributes['avatar'] = $value;
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'reporter_id');
    }

    public function loginHistory(): HasMany
    {
        return $this->hasMany(LoginHistory::class)->latest()->limit(20);
    }

    // ─── Business Logic ───────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->portal_id === null && $this->hasRole('super_admin');
    }

    public function isPortalAdmin(): bool
    {
        return $this->hasRole('portal_admin');
    }

    public function getEffectivePortalId(): int
    {
        if ($this->portal_id) {
            return (int) $this->portal_id;
        }

        $portal = app()->bound('current.portal') ? app('current.portal') : null;
        if ($portal) {
            return (int) $portal->id;
        }

        return (int) (Portal::first()?->id ?? 1);
    }


    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function hasPortalAccess(int $portalId): bool
    {
        return $this->isSuperAdmin() || $this->portal_id === $portalId;
    }

    public function incrementLoginAttempts(): void
    {
        $this->increment('login_attempts');

        $max = config('cms.security.max_login_attempts', 5);
        if ($this->login_attempts >= $max) {
            $minutes = config('cms.security.lockout_minutes', 30);
            $this->update(['locked_until' => now()->addMinutes($minutes)]);
        }
    }

    public function resetLoginAttempts(): void
    {
        $this->update([
            'login_attempts' => 0,
            'locked_until'   => null,
            'last_login_at'  => now(),
            'last_login_ip'  => request()->ip(),
        ]);
    }
}
