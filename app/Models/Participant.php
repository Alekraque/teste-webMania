<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Participant extends Model 
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'expedion_id',
        'name',
        'race'
    ];

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class);
    }
}