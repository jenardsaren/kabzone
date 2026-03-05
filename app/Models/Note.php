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
        'bo_cooperative',
        'bo_calm_regulated',
        'bo_restless_fidgety',
        'bo_easily_frustrated',
        'bo_tantrums',
        'bo_meltdowns',
        'bo_avoidant',
        'bo_aggressive',
        'bo_other',
        'bo_other_details',
    ];

    protected $casts = [
        'bo_cooperative' => 'bool',
        'bo_calm_regulated' => 'bool',
        'bo_restless_fidgety' => 'bool',
        'bo_easily_frustrated' => 'bool',
        'bo_tantrums' => 'bool',
        'bo_meltdowns' => 'bool',
        'bo_avoidant' => 'bool',
        'bo_aggressive' => 'bool',
        'bo_other' => 'bool',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}
