<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\CreateExpeditionRequest;
use App\Models\Expedition;

class ExpeditionController
{
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
            }

            foreach ($data['artifacts'] as $artifact) {
                $expedition->artifacts()->create($artifact);
            }

            DB::commit();

            return response()->json($expedition->load(['participants', 'artifacts']), 201);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
	}
}