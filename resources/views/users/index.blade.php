<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gerenciamento de Usuários</h1>
                <p class="text-sm text-gray-500 mt-1">Controle de acesso ao sistema</p>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between"
                 x-data="{ show: true }" x-show="show" x-transition>
                <span>{{ session('success') }}</span>
                <button type="button" @click="show = false" class="text-green-500 hover:text-green-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nome ou email..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perfil</label>
                    <select name="role"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        @foreach (\App\Enums\UserRole::cases() as $role)
                            <option value="{{ $role->value }}" {{ request('role') === $role->value ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200">
                        Limpar Filtros
                    </button>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cód</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Perfil</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cadastro</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-center">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">#{{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $roleClasses = [
                                            'manager' => 'bg-purple-100 text-purple-800',
                                            'waiter' => 'bg-green-100 text-green-800',
                                            'kitchen' => 'bg-orange-100 text-orange-800',
                                            'customer' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleClasses[$user->role->value] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $user->role->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->status ?? true)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-150"
                                           title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button type="button"
                                                x-data
                                                @click="$dispatch('open-modal', { userId: '{{ $user->uuid }}', userName: '{{ $user->name }}', userStatus: {{ $user->status ? 'true' : 'false' }} })"
                                                class="p-2 {{ $user->status ? 'text-red-600 hover:text-red-900 hover:bg-red-50' : 'text-green-600 hover:text-green-900 hover:bg-green-50' }} rounded-lg transition-colors duration-150"
                                                title="{{ $user->status ? 'Inativar' : 'Ativar' }}">
                                            @if ($user->status)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="py-12">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum usuário encontrado</h3>
                                        <p class="mt-1 text-sm text-gray-500">Tente ajustar os filtros ou cadastre um novo usuário.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
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
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
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
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div x-show="userStatus" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        </div>
                        <div x-show="!userStatus" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title" x-text="actionTitle + ' Usuário'"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Tem certeza que deseja <span x-text="action" class="font-medium"></span> o usuário <span x-text="userName" class="font-medium text-gray-900"></span>?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
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
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
