<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">QR Codes das Mesas</h1>
            <button onclick="window.print()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir Todos
            </button>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Instructions -->
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-medium mb-1">Como usar os QR Codes:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Imprima e cole o QR Code em cada mesa</li>
                        <li>O cliente escaneia com o celular para abrir o cardápio</li>
                        <li>O cliente pode fazer pedidos diretamente pelo celular</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- QR Codes Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 print:grid-cols-2 print:gap-4">
            @foreach($tables as $table)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 print:shadow-none print:border print:break-inside-avoid">
                    <!-- Table Header -->
                    <div class="text-center mb-3 print:mb-2">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 font-bold text-lg print:bg-gray-200 print:text-gray-800">
                            {{ $table->number }}
                        </span>
                        <h3 class="mt-2 font-semibold text-gray-900 dark:text-gray-100 print:text-gray-800">
                            Mesa {{ $table->number }}
                        </h3>
                        @if($table->name)
                            <p class="text-sm text-gray-500 dark:text-gray-400 print:text-gray-600">{{ $table->name }}</p>
                        @endif
                    </div>

                    <!-- QR Code -->
                    <div class="flex justify-center mb-3 print:mb-2">
                        <div class="bg-white p-2 rounded-lg">
                            {!! $table->qr_code_svg !!}
                        </div>
                    </div>

                    <!-- URL (hidden on print) -->
                    <div class="print:hidden">
                        <p class="text-xs text-gray-400 dark:text-gray-500 text-center truncate mb-3" title="{{ $table->menu_url }}">
                            {{ $table->menu_url }}
                        </p>

                        <!-- Actions -->
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('tables.qrcode.show', $table) }}"
                               target="_blank"
                               class="px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md transition-colors">
                                Ampliar
                            </a>
                            <a href="{{ route('tables.qrcode.download', $table) }}"
                               class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 border border-blue-300 dark:border-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/50 rounded-md transition-colors">
                                Baixar
                            </a>
                        </div>
                    </div>

                    <!-- Print footer -->
                    <div class="hidden print:block text-center">
                        <p class="text-xs text-gray-600">Escaneie para fazer seu pedido</p>
                    </div>
                </div>
            @endforeach
        </div>

        @if($tables->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nenhuma mesa ativa encontrada</p>
                <a href="{{ route('tables.index') }}" class="mt-3 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    Gerenciar Mesas
                </a>
            </div>
        @endif
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .max-w-7xl, .max-w-7xl * {
                visibility: visible;
            }
            .max-w-7xl {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }
            .print\:hidden {
                display: none !important;
            }
            .print\:block {
                display: block !important;
            }
        }
    </style>
</x-app-layout>
