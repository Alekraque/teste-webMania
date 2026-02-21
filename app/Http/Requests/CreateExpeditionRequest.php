<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExpeditionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'journey_description' => ['required', 'string'],

            'participants' => ['required', 'array', 'min:1'],
            'participants.*.name' => ['required', 'string'],
            'participants.*.race' => ['required', 'string'],

            'artifacts' => ['required', 'array', 'min:1'],
            'artifacts.*.name' => ['required', 'string'],
            'artifacts.*.type' => ['required', 'string'],
        ];
    }
}
