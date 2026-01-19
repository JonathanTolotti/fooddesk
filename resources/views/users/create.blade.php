<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-6xl mx-auto px-6 py-5">
                <div class="flex items-center gap-4">
                    <a href="{{ route('users.index') }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Novo Usuário</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Preencha os dados abaixo</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-lg mx-auto px-6 py-8">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    {{-- Dados --}}
                    <div class="p-5 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nome completo</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-400 @enderror">
                            @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="login" class="block text-sm font-medium text-gray-700 mb-1.5">Login</label>
                                <input type="text" name="login" id="login" value="{{ old('login') }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('login') border-red-400 @enderror">
                                @error('login')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">E-mail</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-400 @enderror">
                                @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Cargo --}}
                    <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Selecione o cargo</label>
                        @php $oldRole = old('role'); @endphp
                        <div class="grid grid-cols-4 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="manager" class="peer sr-only" {{ $oldRole == 'manager' ? 'checked' : '' }} required>
                                <div class="py-2.5 px-2 text-center text-xs font-medium rounded-lg border-2 border-gray-200 transition-all
                                            peer-checked:border-purple-500 peer-checked:bg-purple-500 peer-checked:text-white
                                            text-gray-600 hover:border-gray-300"
                                     style="--tw-border-opacity: 1;">
                                    Gerente
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="waiter" class="peer sr-only" {{ $oldRole == 'waiter' ? 'checked' : '' }}>
                                <div class="py-2.5 px-2 text-center text-xs font-medium rounded-lg border-2 border-gray-200 transition-all
                                            peer-checked:border-sky-500 peer-checked:bg-sky-500 peer-checked:text-white
                                            text-gray-600 hover:border-gray-300">
                                    Garçom
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="kitchen" class="peer sr-only" {{ $oldRole == 'kitchen' ? 'checked' : '' }}>
                                <div class="py-2.5 px-2 text-center text-xs font-medium rounded-lg border-2 border-gray-200 transition-all
                                            peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white
                                            text-gray-600 hover:border-gray-300">
                                    Cozinha
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="customer" class="peer sr-only" {{ $oldRole == 'customer' ? 'checked' : '' }}>
                                <div class="py-2.5 px-2 text-center text-xs font-medium rounded-lg border-2 border-gray-200 transition-all
                                            peer-checked:border-gray-500 peer-checked:bg-gray-500 peer-checked:text-white
                                            text-gray-600 hover:border-gray-300">
                                    Cliente
                                </div>
                            </label>
                        </div>
                        @error('role')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Senha --}}
                    <div class="p-5 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Senha</label>
                                <input type="password" name="password" id="password" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-400 @enderror">
                                @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ações --}}
                <div class="mt-5 flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            style="background-color: #4f46e5; color: white;"
                            class="px-5 py-2 text-sm font-semibold rounded-lg hover:opacity-90 transition-opacity">
                        Criar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>