<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo - Pedido #{{ $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
            max-width: 300px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
        }

        .info {
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
        }

        .items {
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .items-header {
            display: flex;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .items-header .qty { width: 30px; }
        .items-header .desc { flex: 1; }
        .items-header .price { width: 60px; text-align: right; }

        .item {
            margin-bottom: 5px;
        }

        .item-row {
            display: flex;
        }

        .item-row .qty { width: 30px; }
        .item-row .desc { flex: 1; }
        .item-row .price { width: 60px; text-align: right; }

        .item-customization {
            padding-left: 30px;
            font-size: 10px;
            color: #666;
        }

        .totals {
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .totals .row {
            display: flex;
            justify-content: space-between;
        }

        .totals .total {
            font-weight: bold;
            font-size: 14px;
            margin-top: 5px;
        }

        .payments {
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .payments h3 {
            font-size: 11px;
            margin-bottom: 5px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .footer {
            text-align: center;
            padding-top: 10px;
        }

        .footer p {
            font-size: 11px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        .print-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-btn:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Imprimir Recibo</button>

    <div class="header">
        <h1>FOODDESK</h1>
        <p>Sistema de Gestão</p>
        <p>Tel: (00) 0000-0000</p>
    </div>

    <div class="info">
        <div class="info-row">
            <span>Pedido:</span>
            <span>#{{ $order->id }}</span>
        </div>
        @if($order->table)
            <div class="info-row">
                <span>Mesa:</span>
                <span>{{ $order->table->number }}{{ $order->table->name ? ' - ' . $order->table->name : '' }}</span>
            </div>
        @endif
        @if($order->customer_name)
            <div class="info-row">
                <span>Cliente:</span>
                <span>{{ $order->customer_name }}</span>
            </div>
        @endif
        <div class="info-row">
            <span>Data:</span>
            <span>{{ $order->opened_at->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span>Hora:</span>
            <span>{{ $order->opened_at->format('H:i') }}</span>
        </div>
        <div class="info-row">
            <span>Atendente:</span>
            <span>{{ $order->user->name }}</span>
        </div>
    </div>

    <div class="items">
        <div class="items-header">
            <span class="qty">QTD</span>
            <span class="desc">DESCRIÇÃO</span>
            <span class="price">VALOR</span>
        </div>

        @foreach($order->items->where('status', '!=', 'cancelled') as $item)
            <div class="item">
                <div class="item-row">
                    <span class="qty">{{ $item->quantity }}</span>
                    <span class="desc">{{ $item->product_name }}</span>
                    <span class="price">{{ number_format($item->total_price, 2, ',', '.') }}</span>
                </div>
                @foreach($item->ingredientCustomizations as $custom)
                    <div class="item-customization">
                        {{ $custom->action === 'removed' ? '- Sem' : '+ Com' }} {{ $custom->ingredient_name }}
                        @if($custom->price > 0)
                            (+{{ number_format($custom->price, 2, ',', '.') }})
                        @endif
                    </div>
                @endforeach
                @if($item->notes)
                    <div class="item-customization">Obs: {{ $item->notes }}</div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="totals">
        <div class="row">
            <span>Subtotal:</span>
            <span>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
        </div>
        @if($order->discount > 0)
            <div class="row">
                <span>Desconto:</span>
                <span>- R$ {{ number_format($order->discount, 2, ',', '.') }}</span>
            </div>
        @endif
        <div class="row total">
            <span>TOTAL:</span>
            <span>R$ {{ number_format($order->total, 2, ',', '.') }}</span>
        </div>
    </div>

    @if($order->payments->count() > 0)
        <div class="payments">
            <h3>PAGAMENTOS:</h3>
            @foreach($order->payments as $payment)
                <div class="payment-row">
                    <span>{{ $payment->method_label }}:</span>
                    <span>R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <div class="footer">
        <p>================================</p>
        <p>Obrigado pela preferência!</p>
        <p>================================</p>
        <p style="margin-top: 10px; font-size: 10px;">
            {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>
</body>
</html>
