<?php

namespace App\DTO;


class CreateExpeditionDTO
{
    //promoção de propriedade do constructor, tipo um shorthand
    public function __construct(
        public string $protocol,
        public int $kingdomId,
        public string $journeyDescription,
        public string $status,
        public array $participants,
        public array $artifacts,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            protocol: $data['protocol'],
            kingdomId: $data['kingdom_id'],
            journeyDescription: $data['journey_description'],
            status: $data['status'],
            participants: $data['participants'] ?? [],
            artifacts: $data['artifacts'] ?? [],
        );
    }
}