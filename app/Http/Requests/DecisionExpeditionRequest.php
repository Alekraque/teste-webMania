<?php

namespace App\Http\Requests;

use App\Domain\Enums\ExpeditionStatus;

class DecisionExpeditionRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('decision_reason') && ! $this->has('rejection_reason')) {
            $this->merge([
                'rejection_reason' => $this->input('decision_reason'),
            ]);
        }
    }

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
            'rejection_reason' => [
                'required_if:decision,' . ExpeditionStatus::REJECTED->value,
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.required_if' => 'A justificativa é obrigatória quando a expedição for rejeitada.',
        ];
    }
}
