<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-6xl mx-auto px-6 py-5">
                <div class="flex items-center gap-4">
                    <a href="{{ route('users.index') }}" class="p-2 -ml-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Editar Usuário</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $user->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-lg mx-auto px-6 py-8">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    {{-- Dados --}}
                    <div class="p-5 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nome completo</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autofocus
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-400 dark:border-red-500 @enderror">
                            @error('name')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="login" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Login</label>
                                <input type="text" name="login" id="login" value="{{ old('login', $user->login) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('login') border-red-400 dark:border-red-500 @enderror">
                                @error('login')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">E-mail</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-400 dark:border-red-500 @enderror">
                                @error('email')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Cargo --}}
                    <div class="px-5 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Selecione o cargo</label>
                        @php $currentRole = old('role', $user->role->value); @endphp
                        <div class="grid grid-cols-4 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="manager" class="peer sr-only" {{ $currentRole == 'manager' ? 'checked' : '' }} required>
                                <div class="py-2.5 px-2 text-center text-xs font-medium rounded-lg border-2 border-gray-200 dark:border-gray-600 transition-all
                                            peer-checked:border-purple-500 peer-checked:bg-purple-500 peer-checked:text-white
                                            text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500">
                                    Gerente
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="waiter" class="peer sr-only" {{ $currentRole == 'waiter' ? 'checked' : '' }}>
                                <div class="py-2.5 px-2 text-center text-xs font-medium rounded-lg border-2 border-gray-200 dark:border-gray-600 transition-all
                                            peer-checked:border-sky-500 peer-checked:bg-sky-500 peer-checked:text-white
                                            text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500">
                                    Garçom
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="kitchen" class="peer sr-only" {{ $currentRole == 'kitchen' ? 'checked' : '' }}>
                                <div class="py-2.5 px-2 text-center text-xs font-medium rounded-lg border-2 border-gray-200 dark:border-gray-600 transition-all
                                            peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white
                                            text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500">
                                    Cozinha
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="customer" class="peer sr-only" {{ $currentRole == 'customer' ? 'checked' : '' }}>
                                <div class="py-2.5 px-2 text-center text-xs font-medium rounded-lg border-2 border-gray-200 dark:border-gray-600 transition-all
                                            peer-checked:border-gray-500 peer-checked:bg-gray-500 peer-checked:text-white
                                            text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500">
                                    Cliente
                                </div>
                            </label>
                        </div>
                        @error('role')<p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Status --}}
                    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Status do usuário</label>
                        @php $currentStatus = old('status', $user->status); @endphp
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="1" class="peer sr-only" {{ $currentStatus ? 'checked' : '' }}>
                                <div class="py-2.5 px-4 text-center text-sm font-medium rounded-lg border-2 border-gray-200 dark:border-gray-600 transition-all
                                            peer-checked:border-green-500 peer-checked:bg-green-500 peer-checked:text-white
                                            text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Ativo
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="0" class="peer sr-only" {{ !$currentStatus ? 'checked' : '' }}>
                                <div class="py-2.5 px-4 text-center text-sm font-medium rounded-lg border-2 border-gray-200 dark:border-gray-600 transition-all
                                            peer-checked:border-red-500 peer-checked:bg-red-500 peer-checked:text-white
                                            text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    Inativo
                                </div>
                            </label>
                        </div>
                        @error('status')<p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Senha --}}
                    <div class="p-5 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Alterar Senha</label>
                            <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Opcional</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="password" name="password" id="password" placeholder="Nova senha"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-400 dark:border-red-500 @enderror">
                                @error('password')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirmar"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ações --}}
                <div class="mt-5 flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>