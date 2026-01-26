<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCustomerOrderItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
            'notes' => 'nullable|string|max:500',
            'removed_ingredients' => 'nullable|array',
            'removed_ingredients.*' => 'exists:ingredients,id',
            'added_ingredients' => 'nullable|array',
            'added_ingredients.*' => 'exists:ingredients,id',
        ];
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Selecione um produto.',
            'product_id.exists' => 'Produto não encontrado.',
            'quantity.required' => 'Informe a quantidade.',
            'quantity.integer' => 'Quantidade deve ser um número inteiro.',
            'quantity.min' => 'Quantidade mínima é 1.',
            'quantity.max' => 'Quantidade máxima é 99.',
            'notes.max' => 'Observação deve ter no máximo 500 caracteres.',
            'removed_ingredients.array' => 'Ingredientes removidos inválidos.',
            'removed_ingredients.*.exists' => 'Ingrediente não encontrado.',
            'added_ingredients.array' => 'Ingredientes adicionais inválidos.',
            'added_ingredients.*.exists' => 'Ingrediente não encontrado.',
        ];
    }
}
