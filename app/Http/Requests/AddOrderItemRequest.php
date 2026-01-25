<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'notes' => ['nullable', 'string', 'max:500'],
            'removed_ingredients' => ['nullable', 'array'],
            'removed_ingredients.*.id' => ['required', 'exists:ingredients,id'],
            'removed_ingredients.*.name' => ['required', 'string'],
            'added_ingredients' => ['nullable', 'array'],
            'added_ingredients.*.id' => ['required', 'exists:ingredients,id'],
            'added_ingredients.*.name' => ['required', 'string'],
            'added_ingredients.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'O produto é obrigatório.',
            'product_id.exists' => 'O produto selecionado não existe.',
            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'quantity.min' => 'A quantidade mínima é 1.',
            'quantity.max' => 'A quantidade máxima é 99.',
            'notes.max' => 'As observações não podem ter mais de 500 caracteres.',
        ];
    }
}
