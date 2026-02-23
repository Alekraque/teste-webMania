<?php

namespace App\DTO;

use App\Domain\Enums\ExpeditionStatus;

class DecisionExpeditionDTO
{
    public function __construct(
        public ExpeditionStatus $decision,
        public ?string $rejectionReason,
        public int $councilMemberId,
    ) {}

    public static function fromArray(array $data, int $councilMemberId): self
    {
        return new self(
            decision: ExpeditionStatus::from($data['decision']),
            rejectionReason: $data['rejection_reason'] ?? null,
            councilMemberId: $councilMemberId,
        );
    }
}
