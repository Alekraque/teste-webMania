<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Expedition;


class ExpeditionDecisionController extends Controller
{
    public function decide(Request $request, $protocol)
    {
        $member = Auth::guard('council')->user();

        $request->validate([
            'decision' => ['required', 'in:APPROVED,REJECTED'],
            'decision_reason' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->decision === 'REJECTED' && empty($value)) {
                        $fail('A justificativa é obrigatória quando a expedição for rejeitada.');
                    }
                }
            ],
        ]);

        $expedition = Expedition::where('protocol', $protocol)->firstOrFail();

        if (in_array($expedition->status, ['APPROVED', 'REJECTED'])) {
            return response()->json([
                'message' => 'Esta expedição já foi decidida por um conselheiro.'
            ], 409);
        }

        $expedition->update([
            'status' => $request->decision,
            'decision_reason' => $request->decision === 'REJECTED'
                ? $request->decision_reason
                : null,
            'decision_by' => $member->id,
            'decision_at' => now(),
        ]);

        return response()->json([
            'message' => 'Expedição avaliada com sucesso',
            'status' => $expedition->status,
            'conselheiro_avaliador' => $member->name,
        ], 200);
    }
}
