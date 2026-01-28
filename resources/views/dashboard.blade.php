<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Dashboard</h1>
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-data="{ time: new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }) }"
                      x-init="setInterval(() => time = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }), 1000)"
                      x-text="time"></span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="dashboardManager()" x-init="init()">
        <!-- Loading State -->
        <div x-show="loading" class="flex justify-center items-center py-12">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div x-show="!loading" x-cloak>
            <!-- Section: Resumo do Dia -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Resumo do Dia
                </h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Faturamento -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Faturamento</p>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1" x-text="formatCurrency(todayStats.revenue)"></p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Pedidos Fechados -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pedidos Fechados</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1" x-text="todayStats.orders_count"></p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Medio -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ticket Medio</p>
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1" x-text="formatCurrency(todayStats.average_ticket)"></p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Taxa de Servico -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Taxa de Servico</p>
                                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1" x-text="formatCurrency(todayStats.service_fee)"></p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Situacao Atual -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Situacao Atual
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Pedidos Abertos -->
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-sm p-5 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-100">Pedidos Abertos</p>
                                <p class="text-3xl font-bold mt-1" x-text="currentSituation.open_orders"></p>
                                <p class="text-sm text-green-100 mt-1" x-text="formatCurrency(currentSituation.open_orders_value) + ' em aberto'"></p>
                            </div>
                            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Mesas Ocupadas -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-sm p-5 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-100">Mesas Ocupadas</p>
                                <p class="text-3xl font-bold mt-1">
                                    <span x-text="currentSituation.occupied_tables"></span>
                                    <span class="text-lg font-normal text-blue-200">/ <span x-text="currentSituation.total_tables"></span></span>
                                </p>
                                <p class="text-sm text-blue-100 mt-1" x-text="Math.round((currentSituation.occupied_tables / currentSituation.total_tables) * 100 || 0) + '% de ocupacao'"></p>
                            </div>
                            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Itens na Cozinha -->
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-sm p-5 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-yellow-100">Itens na Cozinha</p>
                                <p class="text-3xl font-bold mt-1" x-text="currentSituation.kitchen_items"></p>
                                <p class="text-sm text-yellow-100 mt-1">pendentes + preparando</p>
                            </div>
                            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Top Produtos & Pagamentos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top 5 Produtos -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                            Top 5 Produtos do Dia
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-if="topProducts.length === 0">
                            <div class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p>Nenhuma venda registrada hoje</p>
                            </div>
                        </template>
                        <template x-for="(product, index) in topProducts" :key="product.name">
                            <div class="px-5 py-3 flex items-center gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                                     :class="{
                                         'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-400': index === 0,
                                         'bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300': index === 1,
                                         'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-400': index === 2,
                                         'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400': index > 2
                                     }"
                                     x-text="index + 1"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-gray-100 truncate" x-text="product.name"></p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300"
                                          x-text="product.total_sold + ' vendas'"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Pagamentos por Forma -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Pagamentos por Forma
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <!-- Cartao de Credito -->
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Cartao de Credito</p>
                                <div class="mt-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full transition-all duration-500"
                                         :style="'width: ' + getPaymentPercentage('credit_card') + '%'"></div>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="formatCurrency(paymentsByMethod.credit_card?.total || 0)"></p>
                        </div>

                        <!-- Cartao de Debito -->
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Cartao de Debito</p>
                                <div class="mt-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                                         :style="'width: ' + getPaymentPercentage('debit_card') + '%'"></div>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="formatCurrency(paymentsByMethod.debit_card?.total || 0)"></p>
                        </div>

                        <!-- Dinheiro -->
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Dinheiro</p>
                                <div class="mt-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full transition-all duration-500"
                                         :style="'width: ' + getPaymentPercentage('cash') + '%'"></div>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="formatCurrency(paymentsByMethod.cash?.total || 0)"></p>
                        </div>

                        <!-- PIX -->
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">PIX</p>
                                <div class="mt-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-teal-600 h-2 rounded-full transition-all duration-500"
                                         :style="'width: ' + getPaymentPercentage('pix') + '%'"></div>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="formatCurrency(paymentsByMethod.pix?.total || 0)"></p>
                        </div>

                        <!-- Total -->
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total em Pagamentos</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100" x-text="formatCurrency(totalPayments)"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Last Update Info -->
            <div class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
                <span>Atualiza automaticamente a cada 60 segundos</span>
                <span class="mx-2">|</span>
                <span>Ultima atualizacao: <span x-text="lastUpdate"></span></span>
            </div>
        </div>
    </div>

    <script>
        function dashboardManager() {
            return {
                loading: true,
                lastUpdate: '',
                todayStats: {
                    revenue: 0,
                    orders_count: 0,
                    average_ticket: 0,
                    service_fee: 0
                },
                currentSituation: {
                    open_orders: 0,
                    open_orders_value: 0,
                    occupied_tables: 0,
                    total_tables: 0,
                    kitchen_items: 0
                },
                topProducts: [],
                paymentsByMethod: {},

                get totalPayments() {
                    return Object.values(this.paymentsByMethod).reduce((sum, p) => sum + (p?.total || 0), 0);
                },

                init() {
                    // Load initial data from server
                    this.todayStats = @json($today_stats ?? []);
                    this.currentSituation = @json($current_situation ?? []);
                    this.topProducts = @json($top_products ?? []);
                    this.paymentsByMethod = @json($payments_by_method ?? []);
                    this.lastUpdate = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    this.loading = false;

                    // Refresh every 60 seconds
                    setInterval(() => this.loadData(), 60000);
                },

                async loadData() {
                    try {
                        const response = await fetch('{{ route('dashboard.data') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        this.todayStats = data.today_stats;
                        this.currentSituation = data.current_situation;
                        this.topProducts = data.top_products;
                        this.paymentsByMethod = data.payments_by_method;
                        this.lastUpdate = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    } catch (error) {
                        console.error('Erro ao carregar dados do dashboard:', error);
                    }
                },

                formatCurrency(value) {
                    return 'R$ ' + (value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                getPaymentPercentage(method) {
                    if (this.totalPayments === 0) return 0;
                    const methodTotal = this.paymentsByMethod[method]?.total || 0;
                    return Math.round((methodTotal / this.totalPayments) * 100);
                }
            }
        }
    </script>
</x-app-layout>
