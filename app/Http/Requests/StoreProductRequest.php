<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_active' => ['boolean'],
            'ingredients' => ['nullable', 'array'],
            'ingredients.*.id' => ['required', 'integer', 'exists:ingredients,id'],
            'ingredients.*.type' => ['required', 'in:base,standard,additional'],
            'ingredients.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'ingredients.*.additional_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do produto é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um valor numérico.',
            'price.min' => 'O preço não pode ser negativo.',
            'price.max' => 'O preço não pode exceder R$ 999.999,99.',
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada não existe.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg ou webp.',
            'image.max' => 'A imagem não pode ter mais de 2MB.',
            'ingredients.array' => 'Os ingredientes devem ser um array.',
            'ingredients.*.id.required' => 'O ID do ingrediente é obrigatório.',
            'ingredients.*.id.exists' => 'Ingrediente não encontrado.',
            'ingredients.*.type.required' => 'O tipo do ingrediente é obrigatório.',
            'ingredients.*.type.in' => 'O tipo do ingrediente deve ser base, padrão ou adicional.',
            'ingredients.*.quantity.numeric' => 'A quantidade deve ser um número.',
            'ingredients.*.quantity.min' => 'A quantidade não pode ser negativa.',
            'ingredients.*.additional_price.numeric' => 'O preço adicional deve ser um número.',
            'ingredients.*.additional_price.min' => 'O preço adicional não pode ser negativo.',
        ];
    }
}
