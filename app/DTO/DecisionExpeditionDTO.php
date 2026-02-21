<?php

namespace App\DTO;

class DecisionExpeditionDTO
{
    public function __construct(
        public string $decision,
        public ?string $reason,
        public int $councilMemberId,
    ) {}

    public static function fromArray(array $data, int $councilMemberId): self
    {
        return new self(
            decision: $data['decision'],
            reason: $data['reason'] ?? null,
            councilMemberId: $councilMemberId,
        );
    }
}