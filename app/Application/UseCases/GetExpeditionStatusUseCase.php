<?php

namespace App\Application\UseCases;

use App\Models\Expedition;

class GetExpeditionStatusUseCase
{
    public function execute(string $protocol): Expedition
    {
        return Expedition::where('protocol', $protocol)->firstOrFail();
    }
}
