<?php

namespace App\Http\Controllers;

use App\Application\UseCases\GetExpeditionStatusUseCase;
use App\Domain\Enums\ExpeditionStatus;
use App\Http\Requests\GetExpeditionStatusRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\CreateExpeditionRequest;
use App\Models\Expedition;

class ExpeditionController
{
    public function status(
        GetExpeditionStatusRequest $request,
        string $protocol,
        GetExpeditionStatusUseCase $useCase
    )
    {
        $expedition = $useCase->execute($protocol);

        $response = [
            'protocol' => $expedition->protocol,
            'status' => $expedition->status->value,
            'last_updated_at' => $expedition->updated_at?->toISOString(),
        ];

        if (
            $expedition->status === ExpeditionStatus::REJECTED &&
            ! empty($expedition->rejection_reason)
        ) {
            $response['rejection_reason'] = $expedition->rejection_reason;
        }

        return response()->json($response);
    }

	public function store(CreateExpeditionRequest $req)
	{
		$data = $req->validated();
        DB::beginTransaction();

        try {
            $expedition = Expedition::create([
                'protocol' => strtoupper(Str::random(10)),
                'kingdom_id' => $data['kingdom_id'], //mudar para fixo dps, agr ta temporário
                'journey_description' => $data['journey_description'],
                'status' => 'PENDING'
            ]);

            foreach ($data['participants'] as $participant) {
                $expedition->participants()->create($participant);
                $expedition->singleParticipants()->create($participant);
            }

            foreach ($data['artifacts'] as $artifact) {
                $expedition->artifacts()->create($artifact);
                $expedition->singleArtifacts()->create($artifact);
            }

            DB::commit();

            return response()->json(
                $expedition->load(['participants', 'artifacts', 'singleParticipants', 'singleArtifacts']
                ), 201);            

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
	}
}
