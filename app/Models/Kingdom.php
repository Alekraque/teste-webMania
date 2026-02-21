<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kingdom extends Model
{
    protected $fillable = [
        'name',
        'api_token',
    ];

    public function expeditions(): HasMany
    {
        return $this->hasMany(Expedition::class);
    }
}
