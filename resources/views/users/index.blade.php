<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gerenciamento de Usuários</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Controle de acesso ao sistema</p>
            </div>
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Usuário
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="userFilter()"
         x-init="init()">
        @if (session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg flex items-center justify-between"
                 x-data="{ show: true }" x-show="show" x-transition>
                <span>{{ session('success') }}</span>
                <button type="button" @click="show = false" class="text-green-500 hover:text-green-700 dark:hover:text-green-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                    <input type="text" x-model="filters.search"
                           @keydown.enter="applyFilters()"
                           placeholder="Nome ou email..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Perfil</label>
                    <select x-model="filters.role"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        @foreach (\App\Enums\UserRole::cases() as $role)
                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select x-model="filters.status"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="active">Ativo</option>
                        <option value="inactive">Inativo</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="button" @click="applyFilters()" :disabled="loading"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-md transition-colors duration-200 flex items-center justify-center">
                        <template x-if="loading">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!loading">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Buscando...' : 'Pesquisar'"></span>
                    </button>
                    <button type="button" @click="clearFilters()"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        Limpar
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cód</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuário</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Perfil</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cadastro</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-center">
                        <template x-for="user in users" :key="user.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="'#' + user.id"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="user.name"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="user.login"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100" x-text="user.email"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                          :class="getRoleClass(user.role)">
                                        <span x-text="user.role_label"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span x-show="user.status" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        Ativo
                                    </span>
                                    <span x-show="!user.status" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                        Inativo
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="user.created_at"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center gap-1">
                                        <a :href="'/users/' + user.uuid + '/edit'"
                                           class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/50 rounded-lg transition-colors duration-150"
                                           title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button type="button"
                                                @click="$dispatch('open-history-modal', { user: user })"
                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-150"
                                                title="Histórico">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                                @click="$dispatch('open-modal', { userId: user.uuid, userName: user.name, userStatus: user.status })"
                                                :class="user.status ? 'text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/50' : 'text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900/50'"
                                                class="p-2 rounded-lg transition-colors duration-150"
                                                :title="user.status ? 'Inativar' : 'Ativar'">
                                            <svg x-show="user.status" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            <svg x-show="!user.status" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="users.length === 0 && !loading">
                            <td colspan="8" class="text-center">
                                <div class="py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum usuário encontrado</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tente ajustar os filtros ou cadastre um novo usuário.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="pagination.last_page > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Mostrando <span x-text="pagination.from" class="font-medium"></span> a <span x-text="pagination.to" class="font-medium"></span> de <span x-text="pagination.total" class="font-medium"></span> resultados
                    </div>
                    <div class="flex gap-2">
                        <button type="button"
                                @click="goToPage(pagination.current_page - 1)"
                                :disabled="pagination.current_page === 1"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-gray-700 dark:text-gray-300">
                            Anterior
                        </button>
                        <template x-for="page in getPageNumbers()" :key="page">
                            <button type="button"
                                    @click="goToPage(page)"
                                    :class="page === pagination.current_page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="px-3 py-1 text-sm border rounded-md"
                                    x-text="page">
                            </button>
                        </template>
                        <button type="button"
                                @click="goToPage(pagination.current_page + 1)"
                                :disabled="pagination.current_page === pagination.last_page"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-gray-700 dark:text-gray-300">
                            Próximo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div x-data="{
            open: false,
            userId: null,
            userName: '',
            userStatus: true,
            get action() {
                return this.userStatus ? 'inativar' : 'ativar';
            },
            get actionTitle() {
                return this.userStatus ? 'Inativar' : 'Ativar';
            }
         }"
         @open-modal.window="open = true; userId = $event.detail.userId; userName = $event.detail.userName; userStatus = $event.detail.userStatus"
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
                 class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div x-show="userStatus" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/50 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        </div>
                        <div x-show="!userStatus" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/50 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100" id="modal-title" x-text="actionTitle + ' Usuário'"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Tem certeza que deseja <span x-text="action" class="font-medium"></span> o usuário <span x-text="userName" class="font-medium text-gray-900 dark:text-gray-100"></span>?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form :action="'/users/' + userId + '/toggle-status'" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                :class="userStatus ? 'bg-red-600 hover:bg-red-500' : 'bg-green-600 hover:bg-green-500'"
                                class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto">
                            <span x-text="actionTitle"></span>
                        </button>
                    </form>
                    <button type="button"
                            @click="open = false"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-600 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 hover:bg-gray-50 dark:hover:bg-gray-500 sm:mt-0 sm:w-auto">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Histórico -->
    <div x-data="historyModal()"
         @open-history-modal.window="openModal($event.detail.user)"
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
                                <span x-text="userName"></span>
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
                <div class="bg-white dark:bg-gray-800 px-6 py-4 flex-1 overflow-y-auto">
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
                                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100" x-text="history.event === 'created' ? 'Criação' : (history.event === 'deleted' ? 'Exclusão' : history.field_label)"></td>
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
                userUuid: null,
                userName: '',
                histories: [],
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0
                },
                openModal(user) {
                    this.userUuid = user.uuid;
                    this.userName = user.name;
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
                        const response = await fetch('/users/' + this.userUuid + '/history?page=' + page + '&per_page=' + this.pagination.per_page, {
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

        function userFilter() {
            return {
                users: [],
                loading: false,
                filters: {
                    search: '',
                    role: '',
                    status: ''
                },
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0,
                    from: 0,
                    to: 0
                },
                roleClasses: {
                    'manager': 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300',
                    'waiter': 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                    'kitchen': 'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300',
                    'customer': 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300',
                },
                init() {
                    this.fetchUsers(1);
                },
                applyFilters() {
                    this.fetchUsers(1);
                },
                async fetchUsers(page) {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route("users.filter") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ...this.filters,
                                page: page
                            })
                        });
                        const data = await response.json();
                        this.users = data.users;
                        this.pagination = data.pagination;
                    } catch (error) {
                        console.error('Erro ao filtrar usuários:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                clearFilters() {
                    this.filters.search = '';
                    this.filters.role = '';
                    this.filters.status = '';
                    this.fetchUsers(1);
                },
                goToPage(page) {
                    if (page >= 1 && page <= this.pagination.last_page) {
                        this.fetchUsers(page);
                    }
                },
                getPageNumbers() {
                    const pages = [];
                    const current = this.pagination.current_page;
                    const last = this.pagination.last_page;

                    let start = Math.max(1, current - 2);
                    let end = Math.min(last, current + 2);

                    if (current <= 3) {
                        end = Math.min(5, last);
                    }
                    if (current >= last - 2) {
                        start = Math.max(1, last - 4);
                    }

                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    return pages;
                },
                getRoleClass(role) {
                    return this.roleClasses[role] || 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300';
                }
            }
        }
    </script>
</x-app-layout>
