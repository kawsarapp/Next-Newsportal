<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public const UPDATED_AT = null;
    public const CREATED_AT = 'created_at';

    protected $fillable = ['portal_id','user_id','action','subject_type','subject_id','old_values','new_values','context','ip_address','user_agent','created_at'];
    protected $casts    = ['old_values'=>'array','new_values'=>'array','context'=>'array','created_at'=>'datetime'];

    protected static function booted(): void
    {
        static::creating(function (AuditLog $log) {
            if (empty($log->created_at)) {
                $log->created_at = now();
            }
        });
    }
}
