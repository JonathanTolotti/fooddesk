<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Identificação - Mesa {{ $table->number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-600 to-blue-800 min-h-screen flex items-center justify-center p-4" x-data="identifyCustomer()">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-white rounded-full mx-auto flex items-center justify-center shadow-lg mb-4">
                <span class="text-3xl font-bold text-blue-600">{{ $table->number }}</span>
            </div>
            <h1 class="text-2xl font-bold text-white">Mesa {{ $table->number }}</h1>
            <p class="text-blue-200 mt-1">Identifique-se para fazer seu pedido</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Step 1: Phone Search -->
            <div x-show="step === 'phone'" x-transition>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Qual seu telefone?</h2>

                    <div class="space-y-4">
                        <div>
                            <input type="tel"
                                   x-model="phone"
                                   @keyup.enter="searchCustomer()"
                                   placeholder="(00) 00000-0000"
                                   x-mask="(99) 99999-9999"
                                   inputmode="tel"
                                   class="w-full px-4 py-4 text-xl text-center border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all"
                                   :class="error ? 'border-red-500' : ''">
                            <p x-show="error" x-text="error" class="text-red-500 text-sm mt-2 text-center"></p>
                        </div>

                        <button @click="searchCustomer()"
                                :disabled="loading || phone.length < 14"
                                class="w-full py-4 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                            <svg x-show="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="loading ? 'Buscando...' : 'Continuar'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Welcome Back (Customer Found) -->
            <div x-show="step === 'welcome'" x-transition x-cloak>
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full mx-auto flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-1">
                        Olá, <span x-text="customer.name"></span>!
                    </h2>
                    <p class="text-gray-500 mb-2" x-text="customer.phone"></p>

                    <!-- Birthday Alert -->
                    <div x-show="customer.is_birthday" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <p class="text-yellow-800 font-medium">
                            Feliz Aniversário!
                        </p>
                    </div>

                    <button @click="proceed()"
                            class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-colors">
                        Fazer Pedido
                    </button>

                    <button @click="step = 'phone'; phone = ''; customer = null"
                            class="w-full mt-3 py-3 text-gray-500 hover:text-gray-700 font-medium transition-colors">
                        Não sou eu
                    </button>
                </div>
            </div>

            <!-- Step 3: Register New Customer -->
            <div x-show="step === 'register'" x-transition x-cloak>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-1">Cadastro rápido</h2>
                    <p class="text-gray-500 text-sm mb-4">Primeira vez aqui? Preencha seus dados.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                            <input type="text"
                                   x-model="registerForm.name"
                                   placeholder="Seu nome completo"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                            <input type="tel"
                                   x-model="phone"
                                   disabled
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                            <input type="date"
                                   x-model="registerForm.birth_date"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                            <p class="text-xs text-gray-400 mt-1">Ganhe uma surpresa no seu aniversário!</p>
                        </div>

                        <p x-show="error" x-text="error" class="text-red-500 text-sm text-center"></p>

                        <button @click="registerCustomer()"
                                :disabled="loading || !registerForm.name"
                                class="w-full py-4 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                            <svg x-show="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="loading ? 'Cadastrando...' : 'Cadastrar e Continuar'"></span>
                        </button>

                        <button @click="step = 'phone'; phone = ''"
                                class="w-full py-3 text-gray-500 hover:text-gray-700 font-medium transition-colors">
                            Voltar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-blue-200 text-sm mt-6">
            Seus dados são usados apenas para melhorar sua experiência
        </p>
    </div>

    <script>
        function identifyCustomer() {
            return {
                tableUuid: '{{ $table->uuid }}',
                step: 'phone', // phone, welcome, register
                phone: '',
                customer: null,
                loading: false,
                error: '',
                registerForm: {
                    name: '',
                    birth_date: '',
                },

                async searchCustomer() {
                    if (this.phone.length < 14) {
                        this.error = 'Digite um telefone válido';
                        return;
                    }

                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch(`/menu/${this.tableUuid}/search-customer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ phone: this.phone }),
                        });

                        const data = await response.json();

                        if (data.found) {
                            this.customer = data.customer;
                            this.step = 'welcome';
                        } else {
                            this.step = 'register';
                        }
                    } catch (error) {
                        this.error = 'Erro de conexão. Tente novamente.';
                    } finally {
                        this.loading = false;
                    }
                },

                async registerCustomer() {
                    if (!this.registerForm.name.trim()) {
                        this.error = 'Informe seu nome';
                        return;
                    }

                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch(`/menu/${this.tableUuid}/register-customer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                name: this.registerForm.name,
                                phone: this.phone,
                                birth_date: this.registerForm.birth_date || null,
                            }),
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.proceed();
                        } else {
                            this.error = data.message || 'Erro ao cadastrar. Tente novamente.';
                        }
                    } catch (error) {
                        this.error = 'Erro de conexão. Tente novamente.';
                    } finally {
                        this.loading = false;
                    }
                },

                proceed() {
                    window.location.reload();
                }
            };
        }
    </script>
</body>
</html>
