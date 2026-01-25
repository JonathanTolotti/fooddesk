<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Recepção</h1>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-gray-600 dark:text-gray-400">Disponível</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-gray-600 dark:text-gray-400">Ocupada</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="text-gray-600 dark:text-gray-400">Reservada</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-gray-600 dark:text-gray-400">Limpeza</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="receptionManager()" x-init="init()">
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

        <!-- Tables Grid -->
        <div x-show="!loading" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            <template x-for="table in tables" :key="table.id">
                <div @click="handleTableClick(table)"
                     class="relative cursor-pointer rounded-xl p-4 border-2 transition-all duration-200 hover:scale-105 hover:shadow-lg"
                     :class="{
                         'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700 hover:border-green-500': table.status === 'available',
                         'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700 hover:border-red-500': table.status === 'occupied',
                         'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-300 dark:border-yellow-700 hover:border-yellow-500': table.status === 'reserved',
                         'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700 hover:border-blue-500': table.status === 'cleaning'
                     }">
                    <!-- Table Number -->
                    <div class="text-center">
                        <span class="text-3xl font-bold"
                              :class="{
                                  'text-green-700 dark:text-green-400': table.status === 'available',
                                  'text-red-700 dark:text-red-400': table.status === 'occupied',
                                  'text-yellow-700 dark:text-yellow-400': table.status === 'reserved',
                                  'text-blue-700 dark:text-blue-400': table.status === 'cleaning'
                              }"
                              x-text="table.number"></span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="table.name || 'Mesa ' + table.number"></p>
                    </div>

                    <!-- Capacity -->
                    <div class="flex items-center justify-center gap-1 mt-2 text-gray-400 dark:text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-xs" x-text="table.capacity"></span>
                    </div>

                    <!-- Order Info (if occupied) -->
                    <div x-show="table.has_order" class="mt-3 pt-3 border-t border-red-200 dark:border-red-800">
                        <p class="text-xs text-gray-600 dark:text-gray-400" x-show="table.order?.customer_name" x-text="table.order?.customer_name"></p>
                        <div class="flex items-center justify-between text-xs mt-1">
                            <span class="text-gray-500 dark:text-gray-400" x-text="table.order?.items_count + ' itens'"></span>
                            <span class="font-medium text-red-600 dark:text-red-400" x-text="'R$ ' + table.order?.total.toFixed(2).replace('.', ',')"></span>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="'Aberto ' + table.order?.opened_at + ' (' + formatDuration(table.order?.duration_minutes) + ')'"></p>
                    </div>

                    <!-- Status Badge -->
                    <div class="absolute top-2 right-2">
                        <span class="w-3 h-3 rounded-full block"
                              :class="{
                                  'bg-green-500': table.status === 'available',
                                  'bg-red-500': table.status === 'occupied',
                                  'bg-yellow-500': table.status === 'reserved',
                                  'bg-blue-500': table.status === 'cleaning'
                              }"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && tables.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nenhuma mesa cadastrada</p>
            <a href="{{ route('tables.index') }}" class="mt-3 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">
                Cadastrar mesas
            </a>
        </div>

        <!-- Modal Abrir Pedido -->
        <div x-show="showOpenOrderModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">
            <div x-show="showOpenOrderModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
                 @click="closeModal()"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="showOpenOrderModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center">
                                <span class="text-xl font-bold text-green-600 dark:text-green-400" x-text="selectedTable?.number"></span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Abrir Pedido</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedTable?.name || 'Mesa ' + selectedTable?.number"></p>
                            </div>
                        </div>

                        <!-- Step 1: Phone Search -->
                        <div x-show="modalStep === 'phone'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Telefone do Cliente
                                </label>
                                <input type="tel"
                                       x-model="customerPhone"
                                       @keydown.enter="searchCustomer()"
                                       x-mask="(99) 99999-9999"
                                       placeholder="(00) 00000-0000"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <p x-show="modalError" x-text="modalError" class="text-red-500 text-sm"></p>
                        </div>

                        <!-- Step 2: Customer Found -->
                        <div x-show="modalStep === 'found'" class="space-y-4">
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-bold" x-text="customer?.name?.charAt(0)?.toUpperCase()"></div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100" x-text="customer?.name"></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="customer?.phone"></p>
                                    </div>
                                </div>
                                <div x-show="customer?.is_birthday" class="mt-3 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-300 dark:border-yellow-700 rounded-md p-2 text-center">
                                    <span class="text-yellow-800 dark:text-yellow-300 text-sm font-medium">Hoje é aniversário!</span>
                                </div>
                            </div>
                            <button @click="modalStep = 'phone'; customer = null; customerPhone = ''"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                Buscar outro cliente
                            </button>
                        </div>

                        <!-- Step 3: Register New Customer -->
                        <div x-show="modalStep === 'register'" class="space-y-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Cliente não encontrado. Cadastre um novo:</p>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
                                <input type="text"
                                       x-model="registerForm.name"
                                       placeholder="Nome completo"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                                <input type="tel"
                                       x-model="customerPhone"
                                       disabled
                                       class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Nascimento</label>
                                <input type="date"
                                       x-model="registerForm.birth_date"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <p x-show="modalError" x-text="modalError" class="text-red-500 text-sm"></p>
                            <button @click="modalStep = 'phone'; customerPhone = ''"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                Voltar
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end gap-3">
                        <button type="button"
                                @click="closeModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                            Cancelar
                        </button>

                        <!-- Phone Step Button -->
                        <button x-show="modalStep === 'phone'"
                                type="button"
                                @click="searchCustomer()"
                                :disabled="searching || customerPhone.length < 14"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 rounded-md transition-colors duration-200 flex items-center gap-2">
                            <svg x-show="searching" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="searching ? 'Buscando...' : 'Buscar Cliente'"></span>
                        </button>

                        <!-- Found Step Button -->
                        <button x-show="modalStep === 'found'"
                                type="button"
                                @click="openOrder()"
                                :disabled="savingOrder"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:bg-green-400 rounded-md transition-colors duration-200 flex items-center gap-2">
                            <svg x-show="savingOrder" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="savingOrder ? 'Abrindo...' : 'Abrir Pedido'"></span>
                        </button>

                        <!-- Register Step Button -->
                        <button x-show="modalStep === 'register'"
                                type="button"
                                @click="registerAndOpenOrder()"
                                :disabled="savingOrder || !registerForm.name"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:bg-gray-400 rounded-md transition-colors duration-200 flex items-center gap-2">
                            <svg x-show="savingOrder" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="savingOrder ? 'Cadastrando...' : 'Cadastrar e Abrir'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function receptionManager() {
            return {
                loading: true,
                tables: [],
                message: { text: '', type: 'success' },
                showOpenOrderModal: false,
                selectedTable: null,
                modalStep: 'phone', // phone, found, register
                customerPhone: '',
                customer: null,
                searching: false,
                savingOrder: false,
                modalError: '',
                registerForm: {
                    name: '',
                    birth_date: '',
                },

                init() {
                    this.loadTables();
                    // Refresh every 30 seconds
                    setInterval(() => this.loadTables(), 30000);
                },

                formatDuration(minutes) {
                    if (!minutes) return '';
                    if (minutes < 60) return minutes + 'min';
                    const hours = Math.floor(minutes / 60);
                    const mins = minutes % 60;
                    return hours + 'h ' + mins + 'min';
                },

                async loadTables() {
                    try {
                        const response = await fetch('{{ route('reception.tables') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        this.tables = data.tables;
                    } catch (error) {
                        console.error('Erro ao carregar mesas:', error);
                        this.showMessage('Erro ao carregar mesas', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                showMessage(text, type = 'success') {
                    this.message = { text, type };
                    setTimeout(() => this.message.text = '', 5000);
                },

                handleTableClick(table) {
                    if (table.status === 'occupied' && table.has_order) {
                        // Redirect to order
                        window.location.href = '/orders/' + table.order.uuid;
                    } else if (table.status === 'available') {
                        // Open modal to create order
                        this.selectedTable = table;
                        this.resetModal();
                        this.showOpenOrderModal = true;
                    } else if (table.status === 'reserved') {
                        this.showMessage('Mesa reservada. Altere o status para abrir um pedido.', 'error');
                    } else if (table.status === 'cleaning') {
                        this.showMessage('Mesa em limpeza. Aguarde a liberação.', 'error');
                    }
                },

                resetModal() {
                    this.modalStep = 'phone';
                    this.customerPhone = '';
                    this.customer = null;
                    this.modalError = '';
                    this.registerForm = { name: '', birth_date: '' };
                },

                closeModal() {
                    this.showOpenOrderModal = false;
                    this.resetModal();
                },

                async searchCustomer() {
                    if (this.customerPhone.length < 14) {
                        this.modalError = 'Digite um telefone válido';
                        return;
                    }

                    this.searching = true;
                    this.modalError = '';

                    try {
                        const response = await fetch('{{ route('customers.search') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ phone: this.customerPhone })
                        });
                        const data = await response.json();

                        if (data.found) {
                            this.customer = data.customer;
                            this.modalStep = 'found';
                        } else {
                            this.modalStep = 'register';
                        }
                    } catch (error) {
                        this.modalError = 'Erro ao buscar cliente';
                    } finally {
                        this.searching = false;
                    }
                },

                async registerAndOpenOrder() {
                    if (!this.registerForm.name) {
                        this.modalError = 'Informe o nome do cliente';
                        return;
                    }

                    this.savingOrder = true;
                    this.modalError = '';

                    try {
                        // Register customer first
                        const registerResponse = await fetch('{{ route('customers.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                name: this.registerForm.name,
                                phone: this.customerPhone,
                                birth_date: this.registerForm.birth_date || null
                            })
                        });
                        const registerData = await registerResponse.json();

                        if (registerResponse.ok) {
                            this.customer = registerData.customer;
                            await this.openOrder();
                        } else {
                            this.modalError = registerData.message || 'Erro ao cadastrar cliente';
                            this.savingOrder = false;
                        }
                    } catch (error) {
                        this.modalError = 'Erro ao cadastrar cliente';
                        this.savingOrder = false;
                    }
                },

                async openOrder() {
                    this.savingOrder = true;
                    try {
                        const response = await fetch('/orders/from-table/' + this.selectedTable.uuid, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                customer_id: this.customer?.id || null,
                                customer_name: this.customer?.name || null
                            })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            // Redirect to order
                            window.location.href = '/orders/' + data.order.uuid;
                        } else {
                            this.showMessage(data.message || 'Erro ao abrir pedido', 'error');
                        }
                    } catch (error) {
                        console.error('Erro ao abrir pedido:', error);
                        this.showMessage('Erro ao abrir pedido', 'error');
                    } finally {
                        this.savingOrder = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
