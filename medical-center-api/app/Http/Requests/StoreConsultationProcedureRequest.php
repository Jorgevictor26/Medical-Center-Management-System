<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationProcedureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'procedure_id' => ['required', 'exists:procedures,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'tooth' => ['nullable', 'string', 'max:50'],
            'observation' => ['nullable', 'string'],
        ];
    }
}
