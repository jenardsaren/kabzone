<?php

namespace App\Models;

use App\Enums\SessionStatus;
use App\Enums\SessionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Session extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'time',
        'type',
        'client_id',
        'therapist_id',
        'assistant_id',
        'description',
        'notes',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'type' => SessionType::class,
            'status' => SessionStatus::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assistant_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function note(): HasOne
    {
        return $this->hasOne(Note::class);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('date')->orderByDesc('time');
    }

    public function getFormattedTimeAttribute(): string
    {
        return Carbon::parse($this->time)->format('g:i A');
    }
}
