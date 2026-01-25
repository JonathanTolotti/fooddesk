<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
            'notes' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'in:pending,preparing,ready,delivered,cancelled'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'quantity.min' => 'A quantidade mínima é 1.',
            'quantity.max' => 'A quantidade máxima é 99.',
            'notes.max' => 'As observações não podem ter mais de 500 caracteres.',
            'status.in' => 'O status selecionado é inválido.',
        ];
    }
}
