<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consultation_id' => [
                'required',
                'exists:consultations,id'
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01'
            ],
            'payment_method' => [
                'required',
                'in:cash,card,transfer,other'
            ],
            'payment_date' => [
                'nullable',
                'date'
            ],
            'notes' => [
                'nullable',
                'string'
            ],
        ];
    }
}
