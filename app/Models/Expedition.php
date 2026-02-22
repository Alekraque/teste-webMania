<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expedition extends Model
{
    use HasUuids;
    protected $fillable = [
        'protocol',
        'kingdom_id',
        'journey_description',
        'status',
        'rejection_reason',
        'decision_by',
        'decided_at',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function kingdom(): BelongsTo
    {
        return $this->belongsTo(Kingdom::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ExpeditionParticipant::class);
    }

    public function artifacts(): HasMany
    {
        return $this->hasMany(ExpeditionArtifact::class);
    }

    public function councilMembers(): BelongsTo 
    {
       return $this->belongsTo(CouncilMember::class, 'decision_by');
    }

        public function singleParticipants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function singleArtifacts(): HasMany
    {
        return $this->hasMany(Artifact::class);
    }
}
