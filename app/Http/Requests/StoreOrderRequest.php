<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_id' => ['nullable', 'exists:tables,id'],
            'type' => ['required', 'in:dine_in,takeaway,delivery,ifood,anota_ai'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'table_id.exists' => 'A mesa selecionada não existe.',
            'type.required' => 'O tipo de pedido é obrigatório.',
            'type.in' => 'O tipo de pedido selecionado é inválido.',
            'customer_name.max' => 'O nome do cliente não pode ter mais de 100 caracteres.',
            'customer_phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'delivery_address.max' => 'O endereço não pode ter mais de 500 caracteres.',
            'notes.max' => 'As observações não podem ter mais de 1000 caracteres.',
        ];
    }
}
