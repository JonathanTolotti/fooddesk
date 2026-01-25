<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - Mesa {{ $table->number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #f9fafb;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            padding: 32px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .table-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #dbeafe;
            color: #2563eb;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 16px;
        }
        .table-title {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
        }
        .table-name {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
        }
        .qr-container {
            background: white;
            padding: 16px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 24px;
        }
        .qr-container svg {
            display: block;
        }
        .instructions {
            font-size: 16px;
            color: #374151;
            margin-bottom: 8px;
        }
        .url {
            font-size: 11px;
            color: #9ca3af;
            word-break: break-all;
        }
        .actions {
            margin-top: 24px;
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        .btn-secondary:hover {
            background: #f9fafb;
        }
        @media print {
            body {
                background: white;
            }
            .card {
                box-shadow: none;
                padding: 0;
            }
            .actions {
                display: none;
            }
            .url {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="table-number">{{ $table->number }}</div>
        <h1 class="table-title">Mesa {{ $table->number }}</h1>
        @if($table->name)
            <p class="table-name">{{ $table->name }}</p>
        @else
            <p class="table-name">&nbsp;</p>
        @endif

        <div class="qr-container">
            {!! $table->qr_code_svg !!}
        </div>

        <p class="instructions">Escaneie para fazer seu pedido</p>
        <p class="url">{{ $table->menu_url }}</p>

        <div class="actions">
            <button onclick="window.print()" class="btn btn-primary">
                Imprimir
            </button>
            <a href="{{ route('tables.qrcodes') }}" class="btn btn-secondary">
                Voltar
            </a>
        </div>
    </div>
</body>
</html>
