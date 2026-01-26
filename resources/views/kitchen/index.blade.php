<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cozinha - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div x-data="kitchenManager()" x-init="init()" class="min-h-screen">
        <!-- Header -->
        <header class="bg-gray-800 border-b border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-bold">Cozinha</h1>
                    <span class="px-3 py-1 bg-orange-600 rounded-full text-sm font-medium" x-text="totalItems + ' item(ns) em preparo'"></span>
                </div>
                <div class="flex items-center gap-4">
                    <!-- View Toggle -->
                    <div class="flex items-center bg-gray-700 rounded-lg p-1">
                        <button @click="viewMode = 'items'"
                                :class="viewMode === 'items' ? 'bg-gray-600' : ''"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors">
                            Por Item
                        </button>
                        <button @click="viewMode = 'orders'"
                                :class="viewMode === 'orders' ? 'bg-gray-600' : ''"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors">
                            Por Pedido
                        </button>
                    </div>
                    <!-- Clock -->
                    <div class="text-xl font-mono" x-text="currentTime"></div>
                    <!-- Back Link -->
                    <a href="{{ route('dashboard') }}" class="p-2 hover:bg-gray-700 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                </div>
            </div>
        </header>

        <!-- Loading State -->
        <div x-show="loading" class="flex justify-center items-center py-24">
            <svg class="animate-spin h-12 w-12 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && totalItems === 0" class="flex flex-col items-center justify-center py-24">
            <svg class="w-24 h-24 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-2xl text-gray-500">Nenhum item em preparo</p>
            <p class="text-gray-600 mt-2">Aguardando novos pedidos...</p>
        </div>

        <!-- Items View -->
        <div x-show="!loading && viewMode === 'items'" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <template x-for="item in items" :key="item.id">
                    <div class="rounded-xl overflow-hidden transition-all duration-300"
                         :class="{
                             'bg-gray-800 border-2 border-gray-700': item.waiting_minutes < yellowMinutes,
                             'bg-yellow-900/50 border-2 border-yellow-600': item.waiting_minutes >= yellowMinutes && item.waiting_minutes < redMinutes,
                             'bg-red-900/50 border-2 border-red-600 animate-pulse': item.waiting_minutes >= redMinutes
                         }">
                        <!-- Item Header -->
                        <div class="px-4 py-3 border-b border-gray-700 flex items-center justify-between"
                             :class="{
                                 'bg-gray-700': item.waiting_minutes < yellowMinutes,
                                 'bg-yellow-800': item.waiting_minutes >= yellowMinutes && item.waiting_minutes < redMinutes,
                                 'bg-red-800': item.waiting_minutes >= redMinutes
                             }">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold"
                                     x-text="item.table_number || '#'"></div>
                                <div>
                                    <span class="text-orange-400 font-bold">#<span x-text="item.order_id"></span></span>
                                    <span class="mx-1 text-gray-500">•</span>
                                    <span class="font-medium" x-text="item.display_name"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-400" x-text="item.sent_at"></p>
                                <p class="font-bold"
                                   :class="{
                                       'text-green-400': item.waiting_minutes < yellowMinutes,
                                       'text-yellow-400': item.waiting_minutes >= yellowMinutes && item.waiting_minutes < redMinutes,
                                       'text-red-400': item.waiting_minutes >= redMinutes
                                   }"
                                   x-text="item.waiting_minutes + ' min'"></p>
                            </div>
                        </div>

                        <!-- Item Content -->
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <p class="text-2xl font-bold" x-text="item.quantity + 'x ' + item.product_name"></p>
                                    <!-- Customizations -->
                                    <div x-show="item.customizations.length > 0" class="mt-2 space-y-1">
                                        <template x-for="custom in item.customizations" :key="custom.ingredient_name">
                                            <p class="text-lg"
                                               :class="custom.action === 'removed' ? 'text-red-400' : 'text-green-400'"
                                               x-text="custom.display_text"></p>
                                        </template>
                                    </div>
                                    <!-- Notes -->
                                    <p x-show="item.notes" class="mt-2 text-lg text-yellow-300 font-medium" x-text="'OBS: ' + item.notes"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Item Action -->
                        <div class="px-4 pb-4">
                            <button @click="markReady(item)"
                                    class="w-full py-3 text-lg font-bold bg-green-600 hover:bg-green-500 rounded-lg transition-colors">
                                PRONTO
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Orders View -->
        <div x-show="!loading && viewMode === 'orders'" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <template x-for="order in byOrder" :key="order.order_id">
                    <div class="rounded-xl overflow-hidden transition-all duration-300"
                         :class="{
                             'bg-gray-800 border-2 border-gray-700': order.oldest_item_minutes < yellowMinutes,
                             'bg-yellow-900/50 border-2 border-yellow-600': order.oldest_item_minutes >= yellowMinutes && order.oldest_item_minutes < redMinutes,
                             'bg-red-900/50 border-2 border-red-600 animate-pulse': order.oldest_item_minutes >= redMinutes
                         }">
                        <!-- Order Header -->
                        <div class="px-4 py-3 border-b border-gray-700 flex items-center justify-between"
                             :class="{
                                 'bg-gray-700': order.oldest_item_minutes < yellowMinutes,
                                 'bg-yellow-800': order.oldest_item_minutes >= yellowMinutes && order.oldest_item_minutes < redMinutes,
                                 'bg-red-800': order.oldest_item_minutes >= redMinutes
                             }">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-xl font-bold"
                                     x-text="order.table_number || '#'"></div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-orange-400 font-bold">#<span x-text="order.order_id"></span></span>
                                        <span class="text-gray-500">•</span>
                                        <span class="font-bold text-lg" x-text="order.display_name"></span>
                                    </div>
                                    <p class="text-sm text-gray-400" x-text="order.items_count + ' item(ns)'"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold"
                                   :class="{
                                       'text-green-400': order.oldest_item_minutes < yellowMinutes,
                                       'text-yellow-400': order.oldest_item_minutes >= yellowMinutes && order.oldest_item_minutes < redMinutes,
                                       'text-red-400': order.oldest_item_minutes >= redMinutes
                                   }"
                                   x-text="order.oldest_item_minutes + ' min'"></p>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="divide-y divide-gray-700 max-h-80 overflow-y-auto">
                            <template x-for="item in order.items" :key="item.id">
                                <div class="p-4 flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <p class="text-xl font-bold" x-text="item.quantity + 'x ' + item.product_name"></p>
                                        <!-- Customizations -->
                                        <template x-for="custom in item.customizations" :key="custom.ingredient_name">
                                            <p class="text-base"
                                               :class="custom.action === 'removed' ? 'text-red-400' : 'text-green-400'"
                                               x-text="custom.display_text"></p>
                                        </template>
                                        <p x-show="item.notes" class="text-base text-yellow-300 font-medium" x-text="'OBS: ' + item.notes"></p>
                                    </div>
                                    <button @click="markReady(item)"
                                            class="px-4 py-2 text-sm font-bold bg-green-600 hover:bg-green-500 rounded-lg transition-colors flex-shrink-0">
                                        PRONTO
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Order Action -->
                        <div class="px-4 py-3 border-t border-gray-700 bg-gray-800/50">
                            <button @click="markOrderReady(order)"
                                    class="w-full py-3 text-lg font-bold bg-green-600 hover:bg-green-500 rounded-lg transition-colors">
                                TUDO PRONTO
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Audio for new items -->
        <audio id="alertSound" preload="auto">
            <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2Onp+cnJmVlpyfo52bmZGJfnVxcnd+h5KYnJuZl5OTl5udnpyZlI6FeXJtb3V+iZSbnp2bmJSSlZqdnpyYko2CdnBrb3aBjJednpqWkZCSlpqcm5eRi4F1bWpscnyIlJ2fnJeTkZKWmpudmZSNgndwbW1xeoWQm5+dmpiUk5aZnJ2alI2DdnFtbnJ8hpGbnpyYlZKSlZmbnJmUjYN2cG5vdHyHkpudm5eUkZKVmZybl5GMgnZwbm9zfIeSm52bl5SRkpWZnJuYlIyCdnBub3N8iJOcnZuXlJGSlZmcm5iUjIJ2cG5vc3yIk5ydm5eUkZKVmZybmJSMgnZwbm9zfIiTnJ2bl5SRkpWZnJuYlIyCdnBub3N8iJOcnZuXlJGSlZmcm5iUjA==" type="audio/wav">
        </audio>
    </div>

    <script>
        function kitchenManager() {
            return {
                loading: true,
                items: [],
                byOrder: [],
                totalItems: 0,
                viewMode: 'orders',
                currentTime: '',
                previousCount: 0,
                yellowMinutes: {{ $kitchenSettings['yellow_minutes'] }},
                redMinutes: {{ $kitchenSettings['red_minutes'] }},
                refreshSeconds: {{ $kitchenSettings['refresh_seconds'] }},

                init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);
                    this.loadItems();
                    // Refresh based on settings
                    setInterval(() => this.loadItems(), this.refreshSeconds * 1000);
                },

                updateClock() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                },

                async loadItems() {
                    try {
                        const response = await fetch('{{ route('kitchen.items') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();

                        // Play sound if new items
                        if (data.total_items > this.previousCount && this.previousCount > 0) {
                            this.playAlert();
                        }
                        this.previousCount = data.total_items;

                        this.items = data.items;
                        this.byOrder = data.by_order;
                        this.totalItems = data.total_items;
                    } catch (error) {
                        console.error('Erro ao carregar itens:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                playAlert() {
                    try {
                        const audio = document.getElementById('alertSound');
                        if (audio) {
                            audio.currentTime = 0;
                            audio.play().catch(() => {});
                        }
                    } catch (e) {}
                },

                async markReady(item) {
                    try {
                        const response = await fetch('/kitchen/items/' + item.uuid + '/ready', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) {
                            // Remove from list
                            this.items = this.items.filter(i => i.id !== item.id);
                            this.byOrder = this.byOrder.map(o => ({
                                ...o,
                                items: o.items.filter(i => i.id !== item.id)
                            })).filter(o => o.items.length > 0);
                            this.totalItems--;
                        }
                    } catch (error) {
                        console.error('Erro ao marcar como pronto:', error);
                    }
                },

                async markOrderReady(order) {
                    try {
                        const response = await fetch('/kitchen/orders/' + order.order_uuid + '/ready', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (response.ok) {
                            // Remove all items of this order
                            const itemIds = order.items.map(i => i.id);
                            this.items = this.items.filter(i => !itemIds.includes(i.id));
                            this.byOrder = this.byOrder.filter(o => o.order_id !== order.order_id);
                            this.totalItems -= data.count;
                        }
                    } catch (error) {
                        console.error('Erro ao marcar pedido como pronto:', error);
                    }
                }
            }
        }
    </script>
</body>
</html>
