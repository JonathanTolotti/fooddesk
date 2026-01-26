<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Establishment
            [
                'key' => 'restaurant_name',
                'category' => 'establishment',
                'type' => 'string',
                'value' => 'FoodDesk',
                'default_value' => 'FoodDesk',
                'label' => 'Nome do Estabelecimento',
                'description' => 'Nome que aparece no recibo e em outras áreas do sistema',
                'is_public' => true,
            ],
            [
                'key' => 'restaurant_address',
                'category' => 'establishment',
                'type' => 'string',
                'value' => '',
                'default_value' => '',
                'label' => 'Endereço',
                'description' => 'Endereço completo do estabelecimento',
                'is_public' => true,
            ],
            [
                'key' => 'restaurant_phone',
                'category' => 'establishment',
                'type' => 'string',
                'value' => '',
                'default_value' => '',
                'label' => 'Telefone',
                'description' => 'Telefone para contato',
                'is_public' => true,
            ],
            [
                'key' => 'receipt_footer_message',
                'category' => 'establishment',
                'type' => 'string',
                'value' => 'Obrigado pela preferência!',
                'default_value' => 'Obrigado pela preferência!',
                'label' => 'Mensagem do Rodapé do Recibo',
                'description' => 'Mensagem exibida no final do recibo',
                'is_public' => false,
            ],

            // Service
            [
                'key' => 'service_fee_enabled',
                'category' => 'service',
                'type' => 'boolean',
                'value' => '1',
                'default_value' => '1',
                'label' => 'Habilitar Taxa de Serviço',
                'description' => 'Ativa ou desativa a taxa de serviço',
                'is_public' => false,
            ],
            [
                'key' => 'service_fee_type',
                'category' => 'service',
                'type' => 'string',
                'value' => 'percentage',
                'default_value' => 'percentage',
                'label' => 'Tipo de Taxa de Serviço',
                'description' => 'Percentual sobre o subtotal ou valor fixo',
                'is_public' => false,
            ],
            [
                'key' => 'service_fee_percentage',
                'category' => 'service',
                'type' => 'float',
                'value' => '10',
                'default_value' => '10',
                'label' => 'Percentual da Taxa de Serviço',
                'description' => 'Percentual aplicado sobre o subtotal (ex: 10 para 10%)',
                'is_public' => false,
            ],
            [
                'key' => 'service_fee_fixed_value',
                'category' => 'service',
                'type' => 'float',
                'value' => '0',
                'default_value' => '0',
                'label' => 'Valor Fixo da Taxa de Serviço',
                'description' => 'Valor fixo em reais quando o tipo for valor fixo',
                'is_public' => false,
            ],

            // Kitchen
            [
                'key' => 'kitchen_alert_yellow_minutes',
                'category' => 'kitchen',
                'type' => 'integer',
                'value' => '10',
                'default_value' => '10',
                'label' => 'Alerta Amarelo (minutos)',
                'description' => 'Tempo em minutos para o item ficar amarelo na cozinha',
                'is_public' => false,
            ],
            [
                'key' => 'kitchen_alert_red_minutes',
                'category' => 'kitchen',
                'type' => 'integer',
                'value' => '20',
                'default_value' => '20',
                'label' => 'Alerta Vermelho (minutos)',
                'description' => 'Tempo em minutos para o item ficar vermelho na cozinha',
                'is_public' => false,
            ],
            [
                'key' => 'kitchen_refresh_seconds',
                'category' => 'kitchen',
                'type' => 'integer',
                'value' => '10',
                'default_value' => '10',
                'label' => 'Intervalo de Atualização (segundos)',
                'description' => 'Intervalo em segundos para atualizar a tela da cozinha',
                'is_public' => false,
            ],

            // Table
            [
                'key' => 'max_tables_count',
                'category' => 'table',
                'type' => 'integer',
                'value' => '50',
                'default_value' => '50',
                'label' => 'Número Máximo de Mesas',
                'description' => 'Quantidade máxima de mesas permitidas',
                'is_public' => false,
            ],
            [
                'key' => 'table_cleanup_minutes',
                'category' => 'table',
                'type' => 'integer',
                'value' => '15',
                'default_value' => '15',
                'label' => 'Tempo de Limpeza (minutos)',
                'description' => 'Tempo que a mesa fica em status de limpeza após fechar pedido',
                'is_public' => false,
            ],
            [
                'key' => 'enable_qr_self_service',
                'category' => 'table',
                'type' => 'boolean',
                'value' => '1',
                'default_value' => '1',
                'label' => 'Habilitar Autoatendimento por QR Code',
                'description' => 'Permite que clientes façam pedidos pelo celular',
                'is_public' => true,
            ],

            // Hours
            [
                'key' => 'opening_time',
                'category' => 'hours',
                'type' => 'time',
                'value' => '11:00',
                'default_value' => '11:00',
                'label' => 'Horário de Abertura',
                'description' => 'Horário que o estabelecimento abre',
                'is_public' => true,
            ],
            [
                'key' => 'closing_time',
                'category' => 'hours',
                'type' => 'time',
                'value' => '23:00',
                'default_value' => '23:00',
                'label' => 'Horário de Fechamento',
                'description' => 'Horário que o estabelecimento fecha',
                'is_public' => true,
            ],
            [
                'key' => 'operating_days',
                'category' => 'hours',
                'type' => 'json',
                'value' => '[0,1,2,3,4,5,6]',
                'default_value' => '[0,1,2,3,4,5,6]',
                'label' => 'Dias de Funcionamento',
                'description' => 'Dias da semana em que o estabelecimento funciona (0=Dom, 6=Sáb)',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $settingData) {
            Setting::updateOrCreate(
                ['key' => $settingData['key']],
                $settingData
            );
        }
    }
}
