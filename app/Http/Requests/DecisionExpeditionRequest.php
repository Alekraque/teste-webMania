<?php

namespace App\Http\Requests;

use App\Domain\Enums\ExpeditionStatus;

class DecisionExpeditionRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'decision' => [
                'required',
                'in:' . implode(',', [
                    ExpeditionStatus::APPROVED->value,
                    ExpeditionStatus::REJECTED->value,
                ]),
            ],
            'decision_reason' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail): void {
                    if ($this->input('decision') === ExpeditionStatus::REJECTED->value && empty($value)) {
                        $fail('A justificativa é obrigatória quando a expedição for rejeitada.');
                    }
                },
            ],
        ];
    }
}
