<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'login' => ['required', 'string', 'max:255', Rule::unique('users', 'login')->ignore($userId)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', Password::defaults(), 'confirmed'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'login.required' => 'O login é obrigatório.',
            'login.unique' => 'Este login já está em uso.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'role.required' => 'O cargo é obrigatório.',
            'role.enum' => 'Selecione um cargo válido.',
            'status.required' => 'O status é obrigatório.',
            'status.boolean' => 'O status deve ser ativo ou inativo.',
        ];
    }
}
