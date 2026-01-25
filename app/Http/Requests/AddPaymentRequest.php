<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'method' => ['required', 'in:credit_card,debit_card,cash,pix'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'method.required' => 'A forma de pagamento é obrigatória.',
            'method.in' => 'A forma de pagamento selecionada é inválida.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'amount.min' => 'O valor mínimo é R$ 0,01.',
            'notes.max' => 'As observações não podem ter mais de 255 caracteres.',
        ];
    }
}
