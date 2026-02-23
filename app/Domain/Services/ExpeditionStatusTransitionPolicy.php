<?php

namespace App\Domain\Services;

use App\Domain\Enums\ExpeditionStatus;

class ExpeditionStatusTransitionPolicy
{
    public function canTransition(ExpeditionStatus $currentStatus, ExpeditionStatus $targetStatus): bool
    {
        if ($this->isTerminal($currentStatus)) {
            return false;
        }

        return in_array($targetStatus, [
            ExpeditionStatus::APPROVED,
            ExpeditionStatus::REJECTED,
        ], true);
    }

    private function isTerminal(ExpeditionStatus $status): bool
    {
        return in_array($status, [
            ExpeditionStatus::APPROVED,
            ExpeditionStatus::REJECTED,
        ], true);
    }
}
