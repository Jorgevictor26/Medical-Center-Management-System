<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'observation' => ['nullable', 'string'],
            'status' => [
                'sometimes',
                'in:waiting,in_progress,completed,cancelled'
            ],
        ];
    }
}