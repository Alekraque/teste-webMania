<?php

namespace App\Http\Requests;

class GetExpeditionStatusRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'protocol' => $this->route('protocol'),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'protocol' => ['required', 'string', 'exists:expeditions,protocol'],
        ];
    }
}
