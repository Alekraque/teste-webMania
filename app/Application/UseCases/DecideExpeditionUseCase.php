<?php

namespace App\Application\UseCases;

use App\DTO\DecisionExpeditionDTO;
use App\Domain\Enums\ExpeditionStatus;
use App\Domain\Exceptions\ExpeditionStatusTransitionNotAllowedException;
use App\Domain\Services\ExpeditionStatusTransitionPolicy;
use App\Models\Expedition;

class DecideExpeditionUseCase
{
    public function __construct(
        private ExpeditionStatusTransitionPolicy $transitionPolicy,
    ) {
    }

    public function execute(string $protocol, DecisionExpeditionDTO $input): Expedition
    {
        $expedition = Expedition::where('protocol', $protocol)->firstOrFail();

        if (! $this->transitionPolicy->canTransition($expedition->status, $input->decision)) {
            throw new ExpeditionStatusTransitionNotAllowedException(
                'Esta expedição já foi decidida por um conselheiro.'
            );
        }

        $expedition->update([
            'status' => $input->decision->value,
            'rejection_reason' => $input->decision === ExpeditionStatus::REJECTED
                ? $input->decisionReason
                : null,
            'decision_by' => $input->councilMemberId,
            'decided_at' => now(),
        ]);

        return $expedition->fresh();
    }
}
