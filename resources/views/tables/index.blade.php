<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Mesas</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerencie as mesas do estabelecimento</p>
            </div>
            <button type="button"
                    @click="$dispatch('open-table-modal', { mode: 'create' })"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Mesa
            </button>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="tableManager()"
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

        <!-- View Toggle & Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- View Toggle -->
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Visualização:</span>
                    <div class="flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                        <button type="button"
                                @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600'"
                                class="px-3 py-1.5 text-sm font-medium transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button type="button"
                                @click="viewMode = 'table'"
                                :class="viewMode === 'table' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600'"
                                class="px-3 py-1.5 text-sm font-medium transition-colors duration-200 border-l border-gray-300 dark:border-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-1 gap-4 md:justify-end">
                    <div class="flex-1 md:max-w-xs">
                        <input type="text" x-model="filters.search"
                               @keydown.enter="fetchTables()"
                               placeholder="Buscar por número ou nome..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>
                    <div>
                        <select x-model="filters.status"
                                @change="fetchTables()"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Todos os status</option>
                            <option value="available">Disponível</option>
                            <option value="occupied">Ocupada</option>
                            <option value="reserved">Reservada</option>
                            <option value="cleaning">Limpeza</option>
                            <option value="active">Ativas</option>
                            <option value="inactive">Inativas</option>
                        </select>
                    </div>
                    <button type="button" @click="fetchTables()" :disabled="loading"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-md transition-colors duration-200 text-sm">
                        <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Status Legend -->
            <div class="flex flex-wrap items-center gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Legenda:</span>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Disponível</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Ocupada</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Reservada</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Limpeza</span>
                </div>
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="viewMode === 'grid'" x-cloak>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                <template x-for="table in tables" :key="table.id">
                    <div class="relative group">
                        <div :class="{
                                'border-green-400 bg-green-50 dark:bg-green-900/20': table.status === 'available' && table.is_active,
                                'border-red-400 bg-red-50 dark:bg-red-900/20': table.status === 'occupied' && table.is_active,
                                'border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20': table.status === 'reserved' && table.is_active,
                                'border-blue-400 bg-blue-50 dark:bg-blue-900/20': table.status === 'cleaning' && table.is_active,
                                'border-gray-300 bg-gray-100 dark:bg-gray-800 opacity-60': !table.is_active
                             }"
                             class="rounded-lg border-2 p-4 transition-all duration-200 hover:shadow-lg cursor-pointer h-36 flex flex-col justify-between"
                             @click="$dispatch('open-table-modal', { mode: 'edit', table: table })">

                            <div>
                                <!-- Table Number -->
                                <div class="text-center">
                                    <span class="text-3xl font-bold text-gray-900 dark:text-gray-100" x-text="table.number"></span>
                                </div>

                                <!-- Table Name -->
                                <div class="text-center mt-1 h-4">
                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="table.name || ''"></span>
                                </div>
                            </div>

                            <div>
                                <!-- Capacity -->
                                <div class="flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="table.capacity"></span>
                                </div>

                                <!-- Status Badge -->
                                <div class="text-center mt-2">
                                    <span :class="{
                                            'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300': table.status === 'available',
                                            'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300': table.status === 'occupied',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300': table.status === 'reserved',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300': table.status === 'cleaning'
                                          }"
                                          class="px-2 py-0.5 text-xs font-medium rounded-full"
                                          x-text="table.status_label"></span>
                                </div>
                            </div>

                            <!-- Inactive Overlay -->
                            <div x-show="!table.is_active" class="absolute inset-0 flex items-center justify-center bg-gray-900/50 rounded-lg">
                                <span class="text-white font-semibold text-sm">Inativa</span>
                            </div>
                        </div>

                        <!-- Quick Actions (on hover) -->
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex gap-1">
                            <button type="button"
                                    @click.stop="$dispatch('open-history-modal', { table: table })"
                                    class="p-1.5 bg-white dark:bg-gray-700 rounded-full shadow-md hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300"
                                    title="Histórico">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                            <button type="button"
                                    @click.stop="toggleStatus(table)"
                                    :class="table.is_active ? 'text-red-600 hover:bg-red-100 dark:hover:bg-red-900/50' : 'text-green-600 hover:bg-green-100 dark:hover:bg-green-900/50'"
                                    class="p-1.5 bg-white dark:bg-gray-700 rounded-full shadow-md"
                                    :title="table.is_active ? 'Inativar' : 'Ativar'">
                                <svg x-show="table.is_active" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                <svg x-show="!table.is_active" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State (Grid) -->
            <div x-show="tables.length === 0 && !loading" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhuma mesa encontrada</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tente ajustar os filtros ou cadastre uma nova mesa.</p>
            </div>
        </div>

        <!-- Table View -->
        <div x-show="viewMode === 'table'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Número</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Capacidade</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Mesa</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ativo</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="table in tables" :key="table.id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100" x-text="table.number"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-500 dark:text-gray-400" x-text="table.name || '-'"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <span class="text-sm text-gray-900 dark:text-gray-100" x-text="table.capacity"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300': table.status === 'available',
                                                'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300': table.status === 'occupied',
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300': table.status === 'reserved',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300': table.status === 'cleaning'
                                              }"
                                              class="px-3 py-1 text-xs font-semibold rounded-full"
                                              x-text="table.status_label"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span x-show="table.is_active" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                            Ativo
                                        </span>
                                        <span x-show="!table.is_active" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                            Inativo
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-1">
                                            <button type="button"
                                                    @click="$dispatch('open-table-modal', { mode: 'edit', table: table })"
                                                    class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/50 rounded-lg transition-colors duration-150"
                                                    title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    @click="$dispatch('open-history-modal', { table: table })"
                                                    class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-150"
                                                    title="Histórico">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    @click="toggleStatus(table)"
                                                    :class="table.is_active ? 'text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/50' : 'text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900/50'"
                                                    class="p-2 rounded-lg transition-colors duration-150"
                                                    :title="table.is_active ? 'Inativar' : 'Ativar'">
                                                <svg x-show="table.is_active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                <svg x-show="!table.is_active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="tables.length === 0 && !loading">
                                <td colspan="6" class="text-center">
                                    <div class="py-12">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhuma mesa encontrada</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tente ajustar os filtros ou cadastre uma nova mesa.</p>
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

        <!-- Pagination for Grid View -->
        <div x-show="viewMode === 'grid' && pagination.total > 0" class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Mostrando
                    <span class="font-medium" x-text="(pagination.current_page - 1) * pagination.per_page + 1"></span>
                    a
                    <span class="font-medium" x-text="Math.min(pagination.current_page * pagination.per_page, pagination.total)"></span>
                    de
                    <span class="font-medium" x-text="pagination.total"></span>
                    mesas
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

    <!-- Modal de Criar/Editar Mesa -->
    <div x-data="{
            open: false,
            mode: 'create',
            saving: false,
            form: {
                uuid: null,
                number: '',
                name: '',
                capacity: 4,
                status: 'available',
                is_active: true
            },
            errors: {},
            get title() {
                return this.mode === 'create' ? 'Nova Mesa' : 'Editar Mesa';
            },
            get buttonText() {
                return this.mode === 'create' ? 'Criar Mesa' : 'Salvar Alterações';
            },
            resetForm() {
                this.form = {
                    uuid: null,
                    number: '',
                    name: '',
                    capacity: 4,
                    status: 'available',
                    is_active: true
                };
                this.errors = {};
            },
            async save() {
                this.errors = {};
                this.saving = true;

                try {
                    const url = this.mode === 'create'
                        ? '{{ route('tables.store') }}'
                        : '/tables/' + this.form.uuid;

                    const method = this.mode === 'create' ? 'POST' : 'PUT';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            number: this.form.number || null,
                            name: this.form.name || null,
                            capacity: this.form.capacity,
                            status: this.form.status,
                            is_active: this.form.is_active
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            this.errors = data.errors;
                        }
                        return;
                    }

                    this.open = false;
                    this.resetForm();
                    window.dispatchEvent(new CustomEvent('table-saved', { detail: data }));
                } catch (error) {
                    console.error('Erro ao salvar mesa:', error);
                } finally {
                    this.saving = false;
                }
            }
         }"
         @open-table-modal.window="
            open = true;
            mode = $event.detail.mode;
            if (mode === 'edit' && $event.detail.table) {
                form = {
                    uuid: $event.detail.table.uuid,
                    number: $event.detail.table.number,
                    name: $event.detail.table.name || '',
                    capacity: $event.detail.table.capacity,
                    status: $event.detail.table.status,
                    is_active: $event.detail.table.is_active
                };
            } else {
                resetForm();
            }
         "
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
             @click="open = false; resetForm()"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <!-- Header -->
                <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="title"></h3>
                        <button type="button" @click="open = false; resetForm()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white dark:bg-gray-800 px-6 py-4">
                    <div class="space-y-4">
                        <!-- Número e Capacidade -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Número
                                </label>
                                <input type="number" x-model="form.number"
                                       placeholder="Auto"
                                       min="1"
                                       class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       :class="errors.number ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deixe vazio para auto</p>
                                <p x-show="errors.number" x-text="errors.number?.[0]" class="mt-1 text-sm text-red-500"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Capacidade <span class="text-red-500">*</span>
                                </label>
                                <input type="number" x-model="form.capacity"
                                       min="1"
                                       max="50"
                                       class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       :class="errors.capacity ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'">
                                <p x-show="errors.capacity" x-text="errors.capacity?.[0]" class="mt-1 text-sm text-red-500"></p>
                            </div>
                        </div>

                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nome (opcional)
                            </label>
                            <input type="text" x-model="form.name"
                                   placeholder="Ex: Varanda, VIP, Área Externa..."
                                   class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   :class="errors.name ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'">
                            <p x-show="errors.name" x-text="errors.name?.[0]" class="mt-1 text-sm text-red-500"></p>
                        </div>

                        <!-- Status da Mesa -->
                        <div x-show="mode === 'edit'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status da Mesa
                            </label>
                            <select x-model="form.status"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="available">Disponível</option>
                                <option value="occupied">Ocupada</option>
                                <option value="reserved">Reservada</option>
                                <option value="cleaning">Limpeza</option>
                            </select>
                        </div>

                        <!-- Ativo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status do Cadastro
                            </label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300" x-text="form.is_active ? 'Ativo' : 'Inativo'"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end gap-3">
                    <button type="button"
                            @click="open = false; resetForm()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="button"
                            @click="save()"
                            :disabled="saving"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 rounded-md transition-colors duration-200 flex items-center">
                        <svg x-show="saving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="saving ? 'Salvando...' : buttonText"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Histórico -->
    <div x-data="historyModal()"
         @open-history-modal.window="openModal($event.detail.table)"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="history-modal-title"
         role="dialog"
         aria-modal="true">

        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
             @click="open = false"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl flex flex-col"
                 style="height: 500px;">

                <!-- Header -->
                <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Histórico de Alterações</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Mesa <span x-text="tableNumber"></span>
                                <span x-show="tableName">(<span x-text="tableName"></span>)</span>
                                <span x-show="pagination.total > 0" class="text-xs">(<span x-text="pagination.total"></span> registros)</span>
                            </p>
                        </div>
                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="bg-white dark:bg-gray-800 px-6 py-4 flex-1 overflow-y-auto custom-scrollbar">
                    <!-- Loading -->
                    <div x-show="loading" class="flex justify-center py-8">
                        <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!loading && histories.length === 0" class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nenhum histórico encontrado.</p>
                    </div>

                    <!-- History Table -->
                    <div x-show="!loading && histories.length > 0">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Usuário</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Campo</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">De</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Para</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="history in histories" :key="history.id">
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap" x-text="history.created_at"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100" x-text="history.user_name"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100" x-text="history.event === 'created' ? 'Criação' : history.field_label"></td>
                                        <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400" x-text="history.old_value"></td>
                                        <td class="px-3 py-2 text-sm text-blue-600 dark:text-blue-400 font-medium" x-text="history.new_value"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer with Pagination -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex items-center justify-between flex-shrink-0">
                    <div class="text-sm text-gray-500 dark:text-gray-400" x-show="pagination.total > 0">
                        Página <span x-text="pagination.current_page"></span> de <span x-text="pagination.last_page"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="goToPage(pagination.current_page - 1)"
                                :disabled="pagination.current_page <= 1 || loading"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            Anterior
                        </button>
                        <button type="button"
                                @click="goToPage(pagination.current_page + 1)"
                                :disabled="pagination.current_page >= pagination.last_page || loading"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            Próxima
                        </button>
                        <button type="button"
                                @click="open = false"
                                class="px-4 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function historyModal() {
            return {
                open: false,
                loading: false,
                tableUuid: null,
                tableNumber: '',
                tableName: '',
                histories: [],
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0
                },
                openModal(table) {
                    this.tableUuid = table.uuid;
                    this.tableNumber = table.number;
                    this.tableName = table.name;
                    this.histories = [];
                    this.pagination = {
                        current_page: 1,
                        last_page: 1,
                        per_page: 10,
                        total: 0
                    };
                    this.open = true;
                    this.loadHistory(1);
                },
                async loadHistory(page) {
                    this.loading = true;
                    try {
                        const response = await fetch('/tables/' + this.tableUuid + '/history?page=' + page + '&per_page=' + this.pagination.per_page, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        this.histories = data.histories;
                        this.pagination = data.pagination;
                    } catch (error) {
                        console.error('Erro ao carregar histórico:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                goToPage(page) {
                    if (page < 1 || page > this.pagination.last_page) return;
                    this.loadHistory(page);
                }
            }
        }

        function tableManager() {
            return {
                tables: [],
                loading: false,
                successMessage: '',
                viewMode: 'grid',
                filters: {
                    search: '',
                    status: ''
                },
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 20,
                    total: 0
                },
                init() {
                    this.fetchTables();

                    window.addEventListener('table-saved', (event) => {
                        this.successMessage = event.detail.message;
                        this.fetchTables();
                        setTimeout(() => this.successMessage = '', 5000);
                    });
                },
                async fetchTables(page = 1) {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('tables.filter') }}', {
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
                        this.tables = data.tables;
                        this.pagination = data.pagination;
                    } catch (error) {
                        console.error('Erro ao buscar mesas:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                goToPage(page) {
                    if (page < 1 || page > this.pagination.last_page) return;
                    this.fetchTables(page);
                },
                async toggleStatus(table) {
                    try {
                        const response = await fetch('/tables/' + table.uuid + '/toggle-status', {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        table.is_active = data.is_active;
                        this.successMessage = data.message;
                        setTimeout(() => this.successMessage = '', 5000);
                    } catch (error) {
                        console.error('Erro ao alterar status:', error);
                    }
                }
            }
        }
    </script>
</x-app-layout>
