<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Pedidos</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerencie os pedidos do estabelecimento</p>
            </div>
            <div class="flex gap-2">
                <button type="button"
                        @click="$dispatch('open-new-order-modal')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Pedido
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="orderManager()"
         x-init="init()">

        <!-- Success Message -->
        <div x-show="successMessage"
             x-transition
             x-cloak
             class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg flex items-center justify-between">
            <span x-text="successMessage"></span>
            <button type="button" @click="successMessage = ''" class="text-green-500 hover:text-green-700 dark:hover:text-green-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Abertos</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="stats.open">0</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fechados Hoje</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="stats.closed">0</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Hoje</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="stats.total">0</p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Faturamento</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400" x-text="'R$ ' + stats.revenue.toFixed(2).replace('.', ',')">R$ 0,00</p>
                    </div>
                    <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-full">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                    <input type="text" x-model="filters.search"
                           @keydown.enter="fetchOrders()"
                           placeholder="Mesa, cliente..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select x-model="filters.status"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Todos</option>
                        <option value="open">Aberto</option>
                        <option value="closed">Fechado</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                    <select x-model="filters.type"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Todos</option>
                        <option value="dine_in">Mesa</option>
                        <option value="takeaway">Balcão</option>
                        <option value="delivery">Delivery</option>
                        <option value="ifood">iFood</option>
                        <option value="anota_ai">Anota Ai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data</label>
                    <input type="date" x-model="filters.date_from"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div class="flex items-end gap-2">
                    <button type="button" @click="fetchOrders()" :disabled="loading"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-md transition-colors duration-200 text-sm flex items-center justify-center">
                        <svg x-show="!loading" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Pesquisar
                    </button>
                    <button type="button" @click="clearFilters()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-sm">
                        Limpar
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mesa/Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Itens</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aberto em</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="order in orders" :key="order.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 cursor-pointer"
                                @click="window.location.href = '/orders/' + order.uuid">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="'#' + order.id"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="order.display_name"></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400" x-text="order.user_name"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="order.type_label"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span :class="{
                                            'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300': order.status === 'open',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300': order.status === 'closed',
                                            'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300': order.status === 'cancelled'
                                          }"
                                          class="px-3 py-1 text-xs font-semibold rounded-full"
                                          x-text="order.status_label"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="order.items_count"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="'R$ ' + order.total.toFixed(2).replace('.', ',')"></span>
                                    <div x-show="order.remaining_amount > 0" class="text-xs text-red-500">
                                        Restam: R$ <span x-text="order.remaining_amount.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="order.opened_at"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center" @click.stop>
                                    <a :href="'/orders/' + order.uuid"
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="orders.length === 0 && !loading">
                            <td colspan="8" class="text-center">
                                <div class="py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum pedido encontrado</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tente ajustar os filtros ou crie um novo pedido.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="pagination.total > 0" class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Mostrando
                        <span class="font-medium" x-text="(pagination.current_page - 1) * pagination.per_page + 1"></span>
                        a
                        <span class="font-medium" x-text="Math.min(pagination.current_page * pagination.per_page, pagination.total)"></span>
                        de
                        <span class="font-medium" x-text="pagination.total"></span>
                        resultados
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="goToPage(pagination.current_page - 1)"
                                :disabled="pagination.current_page <= 1 || loading"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            Anterior
                        </button>
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            Página <span x-text="pagination.current_page"></span> de <span x-text="pagination.last_page"></span>
                        </span>
                        <button type="button"
                                @click="goToPage(pagination.current_page + 1)"
                                :disabled="pagination.current_page >= pagination.last_page || loading"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            Próxima
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Pedido -->
    <div x-data="newOrderModal()"
         @open-new-order-modal.window="open = true"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
             @click="open = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Novo Pedido</h3>
                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 px-6 py-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Pedido</label>
                            <select x-model="form.type"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="dine_in">Mesa</option>
                                <option value="takeaway">Balcão</option>
                                <option value="delivery">Delivery</option>
                            </select>
                        </div>

                        <div x-show="form.type === 'dine_in'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mesa</label>
                            <select x-model="form.table_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Selecione uma mesa</option>
                                <template x-for="table in availableTables" :key="table.id">
                                    <option :value="table.id" x-text="'Mesa ' + table.number + (table.name ? ' - ' + table.name : '')"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Cliente (opcional)</label>
                            <input type="text" x-model="form.customer_name"
                                   placeholder="Nome do cliente"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div x-show="form.type === 'delivery'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                            <input type="text" x-model="form.customer_phone"
                                   placeholder="(00) 00000-0000"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div x-show="form.type === 'delivery'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Endereço de Entrega</label>
                            <textarea x-model="form.delivery_address"
                                      rows="2"
                                      placeholder="Endereço completo"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end gap-3">
                    <button type="button"
                            @click="open = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="button"
                            @click="createOrder()"
                            :disabled="saving || (form.type === 'dine_in' && !form.table_id)"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 rounded-md transition-colors duration-200">
                        <span x-text="saving ? 'Criando...' : 'Criar Pedido'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function orderManager() {
            return {
                orders: [],
                loading: false,
                successMessage: '',
                filters: {
                    search: '',
                    status: '',
                    type: '',
                    date_from: new Date().toLocaleDateString('en-CA')
                },
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0
                },
                stats: {
                    open: 0,
                    closed: 0,
                    total: 0,
                    revenue: 0
                },
                init() {
                    this.fetchOrders();
                    this.fetchStats();

                    // Auto refresh every 30 seconds
                    setInterval(() => {
                        this.fetchOrders(this.pagination.current_page);
                        this.fetchStats();
                    }, 30000);
                },
                async fetchOrders(page = 1) {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('orders.filter') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ...this.filters,
                                page: page,
                                per_page: this.pagination.per_page
                            })
                        });
                        const data = await response.json();
                        this.orders = data.orders;
                        this.pagination = data.pagination;
                    } catch (error) {
                        console.error('Erro ao buscar pedidos:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                async fetchStats() {
                    try {
                        const response = await fetch('{{ route('orders.filter') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                date_from: new Date().toLocaleDateString('en-CA'),
                                per_page: 1000
                            })
                        });
                        const data = await response.json();
                        const orders = data.orders;

                        this.stats.open = orders.filter(o => o.status === 'open').length;
                        this.stats.closed = orders.filter(o => o.status === 'closed').length;
                        this.stats.total = orders.length;
                        this.stats.revenue = orders
                            .filter(o => o.status === 'closed')
                            .reduce((sum, o) => sum + o.total, 0);
                    } catch (error) {
                        console.error('Erro ao buscar estatísticas:', error);
                    }
                },
                goToPage(page) {
                    if (page < 1 || page > this.pagination.last_page) return;
                    this.fetchOrders(page);
                },
                clearFilters() {
                    this.filters.search = '';
                    this.filters.status = '';
                    this.filters.type = '';
                    this.filters.date_from = new Date().toLocaleDateString('en-CA');
                    this.fetchOrders(1);
                }
            }
        }

        function newOrderModal() {
            return {
                open: false,
                saving: false,
                availableTables: [],
                form: {
                    type: 'dine_in',
                    table_id: '',
                    customer_name: '',
                    customer_phone: '',
                    delivery_address: ''
                },
                async init() {
                    await this.fetchTables();
                },
                async fetchTables() {
                    try {
                        const response = await fetch('{{ route('tables.filter') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                status: 'available',
                                per_page: 100
                            })
                        });
                        const data = await response.json();
                        this.availableTables = data.tables.filter(t => t.is_active && t.status === 'available');
                    } catch (error) {
                        console.error('Erro ao buscar mesas:', error);
                    }
                },
                async createOrder() {
                    this.saving = true;
                    try {
                        const response = await fetch('{{ route('orders.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });
                        const data = await response.json();

                        if (response.ok) {
                            window.location.href = '/orders/' + data.order.uuid;
                        }
                    } catch (error) {
                        console.error('Erro ao criar pedido:', error);
                    } finally {
                        this.saving = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
