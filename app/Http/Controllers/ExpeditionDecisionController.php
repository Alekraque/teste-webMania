<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\DTO\DecisionExpeditionDTO;
use App\Http\Requests\DecisionExpeditionRequest;
use App\Application\UseCases\DecideExpeditionUseCase;
use App\Domain\Exceptions\ExpeditionStatusTransitionNotAllowedException;


class ExpeditionDecisionController extends Controller
{
    public function decide(
        DecisionExpeditionRequest $request,
        string $protocol,
        DecideExpeditionUseCase $useCase
    )
    {
        $member = Auth::guard('council')->user();

        if (! $member) {
            return response()->json([
                'message' => 'Usuário não autenticado.',
            ], 401);
        }

        $input = DecisionExpeditionDTO::fromArray($request->validated(), $member->id);

        try {
            $expedition = $useCase->execute($protocol, $input);
        } catch (ExpeditionStatusTransitionNotAllowedException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 409);
        }

        return response()->json([
            'message' => 'Expedição avaliada com sucesso',
            'status' => $expedition->status->value,
            'conselheiro_avaliador' => $member->name,
        ], 200);
    }
}
