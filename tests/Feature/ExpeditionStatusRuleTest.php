<?php

namespace Tests\Feature;

use App\Application\UseCases\DecideExpeditionUseCase;
use App\DTO\DecisionExpeditionDTO;
use App\Domain\Enums\ExpeditionStatus;
use App\Domain\Exceptions\ExpeditionStatusTransitionNotAllowedException;
use App\Models\CouncilMember;
use App\Models\Expedition;
use App\Models\Kingdom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpeditionStatusRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_cannot_change_after_becoming_approved_or_rejected(): void
    {
        $kingdom = Kingdom::create([
            'name' => 'Gondor',
            'email' => 'gondor@example.com',
            'password' => bcrypt('secret'),
        ]);

        $councilMember = CouncilMember::create([
            'name' => 'Denethor',
            'email' => 'denethor@example.com',
            'password' => bcrypt('secret'),
            'role' => 'member',
        ]);

        $expedition = Expedition::create([
            'protocol' => 'PROTO-LOCK-001',
            'kingdom_id' => $kingdom->id,
            'journey_description' => 'Explorar as montanhas do leste',
            'status' => ExpeditionStatus::PENDING->value,
        ]);

        $useCase = app(DecideExpeditionUseCase::class);

        $useCase->execute(
            $expedition->protocol,
            new DecisionExpeditionDTO(
                decision: ExpeditionStatus::APPROVED,
                decisionReason: null,
                councilMemberId: $councilMember->id,
            )
        );

        $this->assertSame(ExpeditionStatus::APPROVED, $expedition->fresh()->status);

        $this->expectException(ExpeditionStatusTransitionNotAllowedException::class);

        $useCase->execute(
            $expedition->protocol,
            new DecisionExpeditionDTO(
                decision: ExpeditionStatus::REJECTED,
                decisionReason: 'Mudança indevida após aprovação',
                councilMemberId: $councilMember->id,
            )
        );
    }
}
