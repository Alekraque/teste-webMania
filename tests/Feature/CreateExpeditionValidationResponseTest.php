<?php

namespace Tests\Feature;

use App\Domain\Enums\ExpeditionStatus;
use App\Models\Kingdom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateExpeditionValidationResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_expedition_returns_json_validation_error_instead_of_redirect(): void
    {
        $response = $this->post('/api/expeditions', []);

        $response->assertStatus(422);
        $this->assertStringContainsString(
            'application/json',
            (string) $response->headers->get('content-type')
        );
        $response->assertJsonStructure([
            'message',
            'errors',
        ]);
    }

    public function test_create_expedition_successfully_returns_201(): void
    {
        $kingdom = Kingdom::create([
            'name' => 'Rohan',
            'email' => 'rohan@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->postJson('/api/expeditions', [
            'kingdom_id' => $kingdom->id,
            'journey_description' => 'Missao de reconhecimento no norte',
            'participants' => [
                ['name' => 'Eomer', 'race' => 'Humano'],
            ],
            'artifacts' => [
                ['name' => 'Lanca Real', 'type' => 'Weapon'],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('status', ExpeditionStatus::PENDING->value);
    }
}
