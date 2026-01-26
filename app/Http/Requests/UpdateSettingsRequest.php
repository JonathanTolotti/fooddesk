<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable'],

            // Establishment
            'settings.restaurant_name' => ['nullable', 'string', 'max:100'],
            'settings.restaurant_address' => ['nullable', 'string', 'max:255'],
            'settings.restaurant_phone' => ['nullable', 'string', 'max:20'],
            'settings.receipt_footer_message' => ['nullable', 'string', 'max:255'],

            // Service
            'settings.service_fee_enabled' => ['nullable'],
            'settings.service_fee_type' => ['nullable', 'string', 'in:percentage,fixed'],
            'settings.service_fee_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'settings.service_fee_fixed_value' => ['nullable', 'numeric', 'min:0'],

            // Kitchen
            'settings.kitchen_alert_yellow_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'settings.kitchen_alert_red_minutes' => ['nullable', 'integer', 'min:1', 'max:120'],
            'settings.kitchen_refresh_seconds' => ['nullable', 'integer', 'min:5', 'max:60'],

            // Table
            'settings.max_tables_count' => ['nullable', 'integer', 'min:1', 'max:999'],
            'settings.table_cleanup_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'settings.enable_qr_self_service' => ['nullable'],

            // Hours
            'settings.opening_time' => ['nullable', 'date_format:H:i'],
            'settings.closing_time' => ['nullable', 'date_format:H:i'],
            'settings.operating_days' => ['nullable', 'array'],
            'settings.operating_days.*' => ['integer', 'min:0', 'max:6'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'settings.required' => 'Nenhuma configuração foi enviada.',
            'settings.restaurant_name.max' => 'O nome do estabelecimento deve ter no máximo 100 caracteres.',
            'settings.restaurant_address.max' => 'O endereço deve ter no máximo 255 caracteres.',
            'settings.restaurant_phone.max' => 'O telefone deve ter no máximo 20 caracteres.',
            'settings.receipt_footer_message.max' => 'A mensagem do rodapé deve ter no máximo 255 caracteres.',
            'settings.service_fee_type.in' => 'O tipo de taxa deve ser percentual ou valor fixo.',
            'settings.service_fee_percentage.numeric' => 'O percentual deve ser um número.',
            'settings.service_fee_percentage.min' => 'O percentual não pode ser negativo.',
            'settings.service_fee_percentage.max' => 'O percentual não pode ser maior que 100.',
            'settings.service_fee_fixed_value.numeric' => 'O valor fixo deve ser um número.',
            'settings.service_fee_fixed_value.min' => 'O valor fixo não pode ser negativo.',
            'settings.kitchen_alert_yellow_minutes.integer' => 'O tempo deve ser um número inteiro.',
            'settings.kitchen_alert_yellow_minutes.min' => 'O tempo mínimo é 1 minuto.',
            'settings.kitchen_alert_yellow_minutes.max' => 'O tempo máximo é 60 minutos.',
            'settings.kitchen_alert_red_minutes.integer' => 'O tempo deve ser um número inteiro.',
            'settings.kitchen_alert_red_minutes.min' => 'O tempo mínimo é 1 minuto.',
            'settings.kitchen_alert_red_minutes.max' => 'O tempo máximo é 120 minutos.',
            'settings.kitchen_refresh_seconds.integer' => 'O intervalo deve ser um número inteiro.',
            'settings.kitchen_refresh_seconds.min' => 'O intervalo mínimo é 5 segundos.',
            'settings.kitchen_refresh_seconds.max' => 'O intervalo máximo é 60 segundos.',
            'settings.max_tables_count.integer' => 'O número de mesas deve ser um número inteiro.',
            'settings.max_tables_count.min' => 'O mínimo é 1 mesa.',
            'settings.max_tables_count.max' => 'O máximo é 999 mesas.',
            'settings.table_cleanup_minutes.integer' => 'O tempo deve ser um número inteiro.',
            'settings.table_cleanup_minutes.min' => 'O tempo mínimo é 1 minuto.',
            'settings.table_cleanup_minutes.max' => 'O tempo máximo é 60 minutos.',
            'settings.opening_time.date_format' => 'O horário de abertura deve estar no formato HH:MM.',
            'settings.closing_time.date_format' => 'O horário de fechamento deve estar no formato HH:MM.',
            'settings.operating_days.array' => 'Os dias de funcionamento devem ser um array.',
            'settings.operating_days.*.integer' => 'Cada dia deve ser um número inteiro.',
            'settings.operating_days.*.min' => 'Os dias devem estar entre 0 (domingo) e 6 (sábado).',
            'settings.operating_days.*.max' => 'Os dias devem estar entre 0 (domingo) e 6 (sábado).',
        ];
    }
}
