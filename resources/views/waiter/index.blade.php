<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Meus Pedidos</h1>
            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="text-gray-600 dark:text-gray-400">Pendente</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-gray-600 dark:text-gray-400">Preparando</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-gray-600 dark:text-gray-400">Pronto</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="waiterManager()" x-init="init()">
        <!-- Success/Error Messages -->
        <div x-show="message.text"
             x-transition
             x-cloak
             :class="message.type === 'success' ? 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-700 dark:text-red-400'"
             class="mb-6 border px-4 py-3 rounded-lg flex items-center justify-between">
            <span x-text="message.text"></span>
            <button type="button" @click="message.text = ''" class="hover:opacity-70">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex justify-center items-center py-12">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Waiter Call Alerts -->
        <div x-show="!loading && callingTables.length > 0" class="mb-6">
            <div class="bg-red-600 rounded-lg shadow-lg p-4 animate-pulse">
                <div class="flex items-center gap-3 mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="text-white font-bold text-lg">Chamando Garçom!</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <template x-for="table in callingTables" :key="table.id">
                        <div class="bg-white rounded-lg px-4 py-2 flex items-center gap-3">
                            <div>
                                <span class="font-bold text-gray-900">Mesa <span x-text="table.number"></span></span>
                                <span class="text-sm text-gray-500 ml-2" x-text="'há ' + table.waiting_minutes + ' min'"></span>
                            </div>
                            <button @click="acknowledgeCall(table)"
                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                                Atender
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div x-show="!loading" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Pedidos Abertos</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="orders.length"></p>
            </div>
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg shadow-sm border border-yellow-200 dark:border-yellow-700 p-4">
                <p class="text-sm text-yellow-600 dark:text-yellow-400">Itens Pendentes</p>
                <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300" x-text="totalPendingItems"></p>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg shadow-sm border border-blue-200 dark:border-blue-700 p-4">
                <p class="text-sm text-blue-600 dark:text-blue-400">Preparando</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300" x-text="totalPreparingItems"></p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg shadow-sm border border-green-200 dark:border-green-700 p-4">
                <p class="text-sm text-green-600 dark:text-green-400">Prontos p/ Entregar</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-300" x-text="totalReadyItems"></p>
            </div>
        </div>

        <!-- Orders Grid -->
        <div x-show="!loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="order in orders" :key="order.id">
                <div class="rounded-lg shadow-sm overflow-hidden transition-colors"
                     :class="order.items_summary.ready > 0
                         ? 'bg-green-50 dark:bg-green-900/20 border-2 border-green-500 dark:border-green-600 ring-2 ring-green-200 dark:ring-green-800'
                         : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700'">
                    <!-- Order Header -->
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                     :class="order.table_number ? 'bg-blue-100 dark:bg-blue-900/50' : 'bg-gray-100 dark:bg-gray-600'">
                                    <span class="font-bold"
                                          :class="order.table_number ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400'"
                                          x-text="order.table_number || '#'"></span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">
                                        <span class="text-blue-600 dark:text-blue-400">#<span x-text="order.id"></span></span>
                                        <span class="mx-1 text-gray-400">•</span>
                                        <span x-text="order.display_name"></span>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="order.opened_at + ' (' + formatDuration(order.duration_minutes) + ')'"></p>
                                </div>
                            </div>
                            <a :href="'/orders/' + order.uuid"
                               class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Items Summary -->
                        <div class="flex items-center gap-3 mt-2">
                            <span x-show="order.items_summary.pending > 0"
                                  class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300"
                                  x-text="order.items_summary.pending + ' pendente(s)'"></span>
                            <span x-show="order.items_summary.preparing > 0"
                                  class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300"
                                  x-text="order.items_summary.preparing + ' preparando'"></span>
                            <span x-show="order.items_summary.ready > 0"
                                  class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300"
                                  x-text="order.items_summary.ready + ' pronto(s)'"></span>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-64 overflow-y-auto custom-scrollbar">
                        <template x-for="item in order.items" :key="item.id">
                            <div class="px-4 py-2 flex items-center justify-between gap-2"
                                 :class="{
                                     'bg-yellow-50 dark:bg-yellow-900/10': item.status === 'pending',
                                     'bg-blue-50 dark:bg-blue-900/10': item.status === 'preparing',
                                     'bg-green-50 dark:bg-green-900/10': item.status === 'ready'
                                 }">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0"
                                              :class="{
                                                  'bg-yellow-500': item.status === 'pending',
                                                  'bg-blue-500': item.status === 'preparing',
                                                  'bg-green-500': item.status === 'ready',
                                                  'bg-gray-400': item.status === 'delivered'
                                              }"></span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate"
                                              x-text="item.quantity + 'x ' + item.product_name"></span>
                                    </div>
                                    <!-- Customizations -->
                                    <div x-show="item.customizations.length > 0" class="ml-4 mt-0.5">
                                        <template x-for="custom in item.customizations" :key="custom.ingredient_name">
                                            <span class="text-xs mr-2"
                                                  :class="custom.action === 'removed' ? 'text-red-500' : 'text-green-600'"
                                                  x-text="custom.display_text"></span>
                                        </template>
                                    </div>
                                    <p x-show="item.notes" class="text-xs text-gray-500 dark:text-gray-400 ml-4 truncate" x-text="'Obs: ' + item.notes"></p>
                                </div>

                                <!-- Item Actions -->
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <template x-if="item.status === 'pending'">
                                        <button @click="sendToKitchen(order, item)"
                                                class="p-1.5 text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded transition-colors"
                                                title="Enviar para cozinha">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </button>
                                    </template>
                                    <template x-if="item.status === 'ready'">
                                        <button @click="markDelivered(order, item)"
                                                class="p-1.5 text-green-600 hover:bg-green-100 dark:hover:bg-green-900/50 rounded transition-colors"
                                                title="Marcar como entregue">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Order Footer -->
                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Total:</span>
                                <span class="font-bold text-gray-900 dark:text-gray-100 ml-1" x-text="'R$ ' + order.total.toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="sendAllPending(order)"
                                        x-show="order.items_summary.pending > 0"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-md transition-colors">
                                    Enviar Pendentes
                                </button>
                                <button @click="deliverAllReady(order)"
                                        x-show="order.items_summary.ready > 0"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">
                                    Entregar Prontos
                                </button>
                                <a :href="'/orders/' + order.uuid"
                                   class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 border border-blue-300 dark:border-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/50 rounded-md transition-colors">
                                    Ver Pedido
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && orders.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nenhum pedido aberto no momento</p>
            <a href="{{ route('reception.index') }}" class="mt-3 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">
                Ir para Recepção
            </a>
        </div>
    </div>

    <script>
        function waiterManager() {
            return {
                loading: true,
                orders: [],
                callingTables: [],
                message: { text: '', type: 'success' },

                get totalPendingItems() {
                    return this.orders.reduce((sum, o) => sum + o.items_summary.pending, 0);
                },

                get totalPreparingItems() {
                    return this.orders.reduce((sum, o) => sum + o.items_summary.preparing, 0);
                },

                get totalReadyItems() {
                    return this.orders.reduce((sum, o) => sum + o.items_summary.ready, 0);
                },

                init() {
                    this.loadOrders();
                    // Refresh every 15 seconds
                    setInterval(() => this.loadOrders(), 15000);

                    // Play sound when new calls arrive
                    this.$watch('callingTables', (newVal, oldVal) => {
                        if (newVal.length > oldVal.length) {
                            this.playAlertSound();
                        }
                    });
                },

                playAlertSound() {
                    // Simple beep sound
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();
                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);
                    oscillator.frequency.value = 800;
                    oscillator.type = 'sine';
                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.3);
                },

                formatDuration(minutes) {
                    if (!minutes) return '';
                    if (minutes < 60) return minutes + 'min';
                    const hours = Math.floor(minutes / 60);
                    const mins = minutes % 60;
                    return hours + 'h ' + mins + 'min';
                },

                async loadOrders() {
                    try {
                        const response = await fetch('{{ route('waiter.orders') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        // Ordenar: pedidos com itens prontos primeiro
                        this.orders = data.orders.sort((a, b) => {
                            if (a.items_summary.ready > 0 && b.items_summary.ready === 0) return -1;
                            if (a.items_summary.ready === 0 && b.items_summary.ready > 0) return 1;
                            return 0;
                        });
                        // Load calling tables
                        this.callingTables = data.calling_tables || [];
                    } catch (error) {
                        console.error('Erro ao carregar pedidos:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async acknowledgeCall(table) {
                    try {
                        const response = await fetch('/waiter/tables/' + table.uuid + '/acknowledge', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) {
                            this.callingTables = this.callingTables.filter(t => t.id !== table.id);
                            this.showMessage('Chamada da Mesa ' + table.number + ' atendida');
                        }
                    } catch (error) {
                        this.showMessage('Erro ao atender chamada', 'error');
                    }
                },

                showMessage(text, type = 'success') {
                    this.message = { text, type };
                    setTimeout(() => this.message.text = '', 5000);
                },

                async sendToKitchen(order, item) {
                    try {
                        const response = await fetch('/orders/' + order.uuid + '/send-to-kitchen', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ item_ids: [item.id] })
                        });
                        if (response.ok) {
                            item.status = 'preparing';
                            item.status_label = 'Preparando';
                            order.items_summary.pending--;
                            order.items_summary.preparing++;
                            this.showMessage('Item enviado para cozinha');
                        }
                    } catch (error) {
                        this.showMessage('Erro ao enviar para cozinha', 'error');
                    }
                },

                async sendAllPending(order) {
                    try {
                        const response = await fetch('/orders/' + order.uuid + '/send-to-kitchen', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (response.ok) {
                            // Update local state
                            order.items.filter(i => i.status === 'pending').forEach(i => {
                                i.status = 'preparing';
                                i.status_label = 'Preparando';
                            });
                            order.items_summary.preparing += order.items_summary.pending;
                            order.items_summary.pending = 0;
                            this.showMessage(data.message);
                        }
                    } catch (error) {
                        this.showMessage('Erro ao enviar para cozinha', 'error');
                    }
                },

                async deliverAllReady(order) {
                    try {
                        const response = await fetch('/orders/' + order.uuid + '/deliver-items', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (response.ok) {
                            // Update local state
                            order.items.filter(i => i.status === 'ready').forEach(i => {
                                i.status = 'delivered';
                                i.status_label = 'Entregue';
                            });
                            order.items_summary.delivered = (order.items_summary.delivered || 0) + order.items_summary.ready;
                            order.items_summary.ready = 0;
                            this.showMessage(data.message);
                        }
                    } catch (error) {
                        this.showMessage('Erro ao entregar itens', 'error');
                    }
                },

                async markDelivered(order, item) {
                    try {
                        const response = await fetch('/orders/' + order.uuid + '/items/' + item.uuid + '/delivered', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) {
                            item.status = 'delivered';
                            item.status_label = 'Entregue';
                            order.items_summary.ready--;
                            order.items_summary.delivered++;
                            this.showMessage('Item marcado como entregue');
                        }
                    } catch (error) {
                        this.showMessage('Erro ao marcar como entregue', 'error');
                    }
                }
            }
        }
    </script>
</x-app-layout>
