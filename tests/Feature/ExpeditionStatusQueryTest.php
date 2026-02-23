<?php

namespace Tests\Feature;

use App\Domain\Enums\ExpeditionStatus;
use App\Models\Expedition;
use App\Models\Kingdom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExpeditionStatusQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_query_requires_authentication(): void
    {
        $response = $this->getJson('/api/expeditions/PROTO-INVALID/status');

        $response->assertStatus(401);
    }

    public function test_status_query_returns_current_status_and_last_update_for_authenticated_user(): void
    {
        $kingdom = Kingdom::create([
            'name' => 'Narnia',
            'email' => 'narnia@example.com',
            'password' => bcrypt('secret'),
        ]);

        Sanctum::actingAs($kingdom, [], 'kingdom');

        $expedition = Expedition::create([
            'protocol' => 'PROTO-STATUS1',
            'kingdom_id' => $kingdom->id,
            'journey_description' => 'Reconhecimento da rota oeste',
            'status' => ExpeditionStatus::PENDING->value,
        ]);

        $response = $this->getJson("/api/expeditions/{$expedition->protocol}/status");

        $response->assertOk();
        $response->assertJsonPath('protocol', $expedition->protocol);
        $response->assertJsonPath('status', ExpeditionStatus::PENDING->value);
        $response->assertJsonStructure([
            'protocol',
            'status',
            'last_updated_at',
        ]);
    }

    public function test_status_query_returns_rejection_reason_when_rejected(): void
    {
        $kingdom = Kingdom::create([
            'name' => 'Mordor',
            'email' => 'mordor@example.com',
            'password' => bcrypt('secret'),
        ]);

        Sanctum::actingAs($kingdom, [], 'kingdom');

        $expedition = Expedition::create([
            'protocol' => 'PROTO-STATUS2',
            'kingdom_id' => $kingdom->id,
            'journey_description' => 'Travessia da montanha sombria',
            'status' => ExpeditionStatus::REJECTED->value,
            'rejection_reason' => 'Risco extremo para a equipe',
        ]);

        $response = $this->getJson("/api/expeditions/{$expedition->protocol}/status");

        $response->assertOk();
        $response->assertJsonPath('status', ExpeditionStatus::REJECTED->value);
        $response->assertJsonPath('rejection_reason', 'Risco extremo para a equipe');
    }
}
