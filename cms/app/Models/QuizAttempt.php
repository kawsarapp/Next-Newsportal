<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'participant_name',
        'participant_email',
        'score',
        'total_questions',
        'passed',
        'ip_address',
    ];

    protected $casts = [
        'passed' => 'boolean',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
