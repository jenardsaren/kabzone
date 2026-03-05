<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'session_id',
        'content',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}
