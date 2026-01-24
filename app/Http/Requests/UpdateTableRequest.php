<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('tables', 'number')->ignore($this->route('table')->id),
            ],
            'name' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'status' => ['sometimes', 'in:available,occupied,reserved,cleaning'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.integer' => 'O número da mesa deve ser um número inteiro.',
            'number.min' => 'O número da mesa deve ser pelo menos 1.',
            'number.unique' => 'Já existe uma mesa com este número.',
            'name.max' => 'O nome não pode ter mais de 100 caracteres.',
            'capacity.required' => 'A capacidade é obrigatória.',
            'capacity.integer' => 'A capacidade deve ser um número inteiro.',
            'capacity.min' => 'A capacidade deve ser pelo menos 1.',
            'capacity.max' => 'A capacidade não pode ser maior que 50.',
            'status.in' => 'O status selecionado é inválido.',
        ];
    }
}
