<?php

namespace Tests\Feature;

use App\Application\UseCases\DecideExpeditionUseCase;
use App\DTO\DecisionExpeditionDTO;
use App\Domain\Enums\ExpeditionStatus;
use App\Domain\Exceptions\ExpeditionStatusTransitionNotAllowedException;
use App\Http\Requests\DecisionExpeditionRequest;
use App\Models\CouncilMember;
use App\Models\Expedition;
use App\Models\Kingdom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
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
                rejectionReason: null,
                councilMemberId: $councilMember->id,
            )
        );

        $this->assertSame(ExpeditionStatus::APPROVED, $expedition->fresh()->status);

        $this->expectException(ExpeditionStatusTransitionNotAllowedException::class);

        $useCase->execute(
            $expedition->protocol,
            new DecisionExpeditionDTO(
                decision: ExpeditionStatus::REJECTED,
                rejectionReason: 'Mudança indevida após aprovação',
                councilMemberId: $councilMember->id,
            )
        );
    }

    public function test_rejection_reason_is_required_when_decision_is_rejected(): void
    {
        $request = new DecisionExpeditionRequest();

        $validator = Validator::make(
            ['decision' => ExpeditionStatus::REJECTED->value],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rejection_reason', $validator->errors()->toArray());
    }
}
