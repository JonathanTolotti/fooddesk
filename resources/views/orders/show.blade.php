<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('orders.index') }}" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Pedido #{{ $order->id }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $order->type_label }} • {{ $order->user?->name ?? 'Autoatendimento' }} • Aberto há <span x-data x-text="formatDuration({{ round($order->opened_at->diffInMinutes(now())) }})"></span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 text-sm font-semibold rounded-full
                    @if($order->status === 'open') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300
                    @elseif($order->status === 'closed') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                    @else bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 @endif">
                    {{ $order->status_label }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="orderDetail()"
         x-init="init()">

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content (Items) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Info Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        @if($order->table)
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Mesa {{ $order->table->number }}</span>
                                @if($order->table->name)
                                    <span class="text-gray-500 dark:text-gray-400">({{ $order->table->name }})</span>
                                @endif
                            </div>
                        @endif
                        @if($order->customer_name)
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-gray-900 dark:text-gray-100">{{ $order->customer_name }}</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-600 dark:text-gray-400">{{ $order->opened_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Items List -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Itens do Pedido</h2>
                        @if($order->isOpen())
                            <button type="button"
                                    @click="$dispatch('open-add-item-modal')"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Adicionar Item
                            </button>
                        @endif
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="item in items" :key="item.id">
                            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-900 dark:text-gray-100" x-text="item.quantity + 'x ' + item.product_name"></span>
                                            <span :class="{
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300': item.status === 'pending',
                                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300': item.status === 'preparing',
                                                    'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300': item.status === 'ready',
                                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': item.status === 'delivered',
                                                    'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300': item.status === 'cancelled'
                                                  }"
                                                  class="px-2 py-0.5 text-xs font-medium rounded-full"
                                                  x-text="item.status_label"></span>
                                        </div>

                                        <!-- Customizations -->
                                        <div x-show="item.customizations.length > 0" class="mt-1 space-y-0.5">
                                            <template x-for="custom in item.customizations" :key="custom.ingredient_name">
                                                <p class="text-xs" :class="custom.action === 'removed' ? 'text-red-500 dark:text-red-400' : 'text-green-600 dark:text-green-400'" x-text="custom.display_text"></p>
                                            </template>
                                        </div>

                                        <!-- Notes -->
                                        <p x-show="item.notes" class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic" x-text="'Obs: ' + item.notes"></p>
                                    </div>

                                    <div class="text-right">
                                        <p class="font-medium text-gray-900 dark:text-gray-100" x-text="'R$ ' + item.total_price.toFixed(2).replace('.', ',')"></p>
                                        <p x-show="item.additions_price > 0" class="text-xs text-gray-500 dark:text-gray-400" x-text="'(+R$ ' + item.additions_price.toFixed(2).replace('.', ',') + ' adicionais)'"></p>
                                    </div>
                                </div>

                                <!-- Item Actions -->
                                <div x-show="order.status === 'open'" class="mt-2 flex items-center gap-2">
                                    <template x-if="item.status !== 'cancelled'">
                                        <button @click="editItem(item)" class="text-xs text-purple-600 dark:text-purple-400 hover:underline">Editar</button>
                                    </template>
                                    <template x-if="item.status === 'pending'">
                                        <button @click="sendItemToKitchen(item)" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Enviar p/ Cozinha</button>
                                    </template>
                                    <template x-if="item.status === 'preparing'">
                                        <button @click="markItemReady(item)" class="text-xs text-green-600 dark:text-green-400 hover:underline">Marcar Pronto</button>
                                    </template>
                                    <template x-if="item.status === 'ready'">
                                        <button @click="markItemDelivered(item)" class="text-xs text-gray-600 dark:text-gray-400 hover:underline">Marcar Entregue</button>
                                    </template>
                                    <template x-if="item.can_be_cancelled">
                                        <button @click="cancelItem(item)" class="text-xs text-red-600 dark:text-red-400 hover:underline">Cancelar</button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div x-show="items.length === 0" class="p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nenhum item no pedido</p>
                            @if($order->isOpen())
                                <button type="button"
                                        @click="$dispatch('open-add-item-modal')"
                                        class="mt-3 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    Adicionar primeiro item
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Send All to Kitchen Button -->
                    <div x-show="order.status === 'open' && items.filter(i => i.status === 'pending').length > 0"
                         class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <button @click="sendAllToKitchen()"
                                class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-md transition-colors duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Enviar Pendentes para Cozinha (<span x-text="items.filter(i => i.status === 'pending').length"></span>)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Summary & Payments) -->
            <div class="space-y-6">
                <!-- Summary Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Resumo</h2>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                            <span class="text-gray-900 dark:text-gray-100" x-text="'R$ ' + order.subtotal.toFixed(2).replace('.', ',')"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Desconto</span>
                            <span class="text-red-600 dark:text-red-400" x-text="'- R$ ' + order.discount.toFixed(2).replace('.', ',')"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600 dark:text-gray-400">Taxa de Serviço (10%)</span>
                                @if($order->isOpen())
                                    <button @click="toggleServiceFee()"
                                            :disabled="savingServiceFee"
                                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                            :class="order.service_fee > 0 ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'"
                                            role="switch"
                                            :aria-checked="order.service_fee > 0">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                              :class="order.service_fee > 0 ? 'translate-x-4' : 'translate-x-0'"></span>
                                    </button>
                                @endif
                            </div>
                            <span class="text-green-600 dark:text-green-400" x-text="'+ R$ ' + order.service_fee.toFixed(2).replace('.', ',')"></span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-3">
                            <span class="text-gray-900 dark:text-gray-100">Total</span>
                            <span class="text-gray-900 dark:text-gray-100" x-text="'R$ ' + order.total.toFixed(2).replace('.', ',')"></span>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Pago</span>
                                <span class="text-green-600 dark:text-green-400" x-text="'R$ ' + order.total_paid.toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div class="flex justify-between text-sm font-medium">
                                <span class="text-gray-600 dark:text-gray-400">Restante</span>
                                <span :class="order.remaining_amount > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'"
                                      x-text="'R$ ' + order.remaining_amount.toFixed(2).replace('.', ',')"></span>
                            </div>
                        </div>

                        @if($order->isOpen())
                            <button @click="discountAmount = order.discount; showDiscountModal = true"
                                    class="w-full mt-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Aplicar Desconto
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Payments Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pagamentos</h2>
                        @if($order->isOpen())
                            <button @click="paymentForm = { method: 'cash', amount: order.remaining_amount }; showPaymentModal = true"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                + Adicionar
                            </button>
                        @endif
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="payment in payments" :key="payment.id">
                            <div class="p-4 flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100" x-text="payment.method_label"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="payment.paid_at"></p>
                                </div>
                                <div class="text-right flex items-center gap-2">
                                    <span class="font-medium text-green-600 dark:text-green-400" x-text="'R$ ' + payment.amount.toFixed(2).replace('.', ',')"></span>
                                    <button x-show="order.status === 'open'"
                                            @click="removePayment(payment)"
                                            class="p-1 text-red-500 hover:text-red-700 dark:hover:text-red-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div x-show="payments.length === 0" class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            Nenhum pagamento registrado
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 space-y-3">
                    @if($order->isOpen())
                        <button @click="closeOrder()"
                                :disabled="!order.is_fully_paid"
                                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium rounded-md transition-colors duration-200">
                            Fechar Conta
                        </button>
                        <p x-show="!order.is_fully_paid" class="text-xs text-center text-red-500">
                            Registre o pagamento total para fechar
                        </p>

                        <button @click="cancelReason = ''; showCancelModal = true"
                                class="w-full px-4 py-2 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 font-medium rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                            Cancelar Pedido
                        </button>
                    @endif

                        <a href="{{ route('orders.receipt', $order) }}" target="_blank"
                           class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Imprimir Recibo
                        </a>

                    <button @click="showHistoryModal = true; loadHistory()"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        Ver Histórico
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Adicionar Item -->
        @include('orders.partials.add-item-modal', ['categories' => $categories])

        <!-- Modal Editar Item -->
        @include('orders.partials.edit-item-modal')

        <!-- Modal Pagamento -->
        @include('orders.partials.payment-modal')

        <!-- Modal Desconto -->
        @include('orders.partials.discount-modal')

        <!-- Modal Cancelar -->
        @include('orders.partials.cancel-modal')

        <!-- Modal Histórico -->
        @include('orders.partials.history-modal')

        <!-- Modal Confirmação -->
        @include('orders.partials.confirm-modal')
    </div>

    <script>
        function formatDuration(minutes) {
            if (minutes < 60) return minutes + 'min';
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            return hours + 'h ' + mins + 'min';
        }

        function orderDetail() {
            return {
                order: @json($orderData),
                items: @json($itemsData),
                payments: @json($paymentsData),
                message: { text: '', type: 'success' },
                showPaymentModal: false,
                showDiscountModal: false,
                showCancelModal: false,
                showHistoryModal: false,

                // Payment modal
                paymentForm: { method: 'cash', amount: 0 },
                savingPayment: false,

                // Discount modal
                discountAmount: 0,
                savingDiscount: false,

                // Service fee
                savingServiceFee: false,

                // Cancel modal
                cancelReason: '',
                savingCancel: false,

                // History modal
                histories: [],
                loadingHistory: false,
                historyPagination: { current_page: 1, last_page: 1, per_page: 10, total: 0 },

                // Confirm modal
                showConfirmModal: false,
                confirmTitle: '',
                confirmMessage: '',
                confirmButtonText: 'Confirmar',
                confirmType: 'danger',
                confirmCallback: null,

                init() {
                    // Refresh data periodically
                    setInterval(() => this.refreshData(), 30000);

                    // Listen for message events from child components
                    window.addEventListener('show-message', (event) => {
                        this.showMessage(event.detail.text, event.detail.type);
                    });
                },

                // Formata valor para exibição (1234.56 -> "1.234,56")
                formatCurrency(value) {
                    if (value === null || value === undefined || value === '') return '';
                    const num = parseFloat(value);
                    if (isNaN(num)) return '';
                    return num.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                },

                // Parse valor formatado para número (1.234,56 -> 1234.56)
                parseCurrency(value) {
                    if (!value) return 0;
                    // Remove pontos de milhar e troca vírgula por ponto
                    const cleaned = value.toString().replace(/\./g, '').replace(',', '.');
                    const num = parseFloat(cleaned);
                    return isNaN(num) ? 0 : num;
                },

                // Aplica máscara de moeda no input
                maskCurrency(event) {
                    let value = event.target.value;

                    // Remove tudo exceto números
                    value = value.replace(/\D/g, '');

                    // Converte para centavos
                    let cents = parseInt(value) || 0;

                    // Formata como moeda
                    let formatted = (cents / 100).toFixed(2).replace('.', ',');

                    // Adiciona separador de milhar
                    formatted = formatted.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    event.target.value = formatted;

                    return cents / 100;
                },

                async refreshData() {
                    try {
                        const response = await fetch('/orders/{{ $order->uuid }}/data');
                        const data = await response.json();
                        this.order = data.order;
                        this.items = data.items;
                        this.payments = data.payments;
                    } catch (error) {
                        console.error('Erro ao atualizar dados:', error);
                    }
                },

                showMessage(text, type = 'success') {
                    this.message = { text, type };
                    setTimeout(() => this.message.text = '', 5000);
                },

                showConfirm(title, message, callback, options = {}) {
                    this.confirmTitle = title;
                    this.confirmMessage = message;
                    this.confirmButtonText = options.buttonText || 'Confirmar';
                    this.confirmType = options.type || 'danger';
                    this.confirmCallback = callback;
                    this.showConfirmModal = true;
                },

                executeConfirm() {
                    if (this.confirmCallback) {
                        this.confirmCallback();
                    }
                    this.showConfirmModal = false;
                    this.confirmCallback = null;
                },

                async sendItemToKitchen(item) {
                    try {
                        const response = await fetch('/orders/{{ $order->uuid }}/send-to-kitchen', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ item_ids: [item.id] })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            item.status = 'preparing';
                            item.status_label = 'Preparando';
                            this.showMessage(data.message);
                        }
                    } catch (error) {
                        this.showMessage('Erro ao enviar para cozinha', 'error');
                    }
                },

                async sendAllToKitchen() {
                    try {
                        const response = await fetch('/orders/{{ $order->uuid }}/send-to-kitchen', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.items.filter(i => i.status === 'pending').forEach(i => {
                                i.status = 'preparing';
                                i.status_label = 'Preparando';
                            });
                            this.showMessage(data.message);
                        }
                    } catch (error) {
                        this.showMessage('Erro ao enviar para cozinha', 'error');
                    }
                },

                async markItemReady(item) {
                    try {
                        const response = await fetch('/orders/{{ $order->uuid }}/items/' + item.uuid + '/ready', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) {
                            item.status = 'ready';
                            item.status_label = 'Pronto';
                            this.showMessage('Item marcado como pronto');
                        }
                    } catch (error) {
                        this.showMessage('Erro ao marcar como pronto', 'error');
                    }
                },

                async markItemDelivered(item) {
                    try {
                        const response = await fetch('/orders/{{ $order->uuid }}/items/' + item.uuid + '/delivered', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) {
                            item.status = 'delivered';
                            item.status_label = 'Entregue';
                            item.can_be_cancelled = false;
                            this.showMessage('Item marcado como entregue');
                        }
                    } catch (error) {
                        this.showMessage('Erro ao marcar como entregue', 'error');
                    }
                },

                editItem(item) {
                    this.$dispatch('open-edit-item-modal', item);
                },

                cancelItem(item) {
                    this.showConfirm(
                        'Cancelar Item',
                        'Tem certeza que deseja cancelar este item?',
                        async () => {
                            try {
                                const response = await fetch('/orders/{{ $order->uuid }}/items/' + item.uuid + '/cancel', {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });
                                if (response.ok) {
                                    item.status = 'cancelled';
                                    item.status_label = 'Cancelado';
                                    item.can_be_cancelled = false;
                                    await this.refreshData();
                                    this.showMessage('Item cancelado');
                                }
                            } catch (error) {
                                this.showMessage('Erro ao cancelar item', 'error');
                            }
                        },
                        { buttonText: 'Cancelar Item', type: 'danger' }
                    );
                },

                removePayment(payment) {
                    this.showConfirm(
                        'Remover Pagamento',
                        'Tem certeza que deseja remover este pagamento?',
                        async () => {
                            try {
                                const response = await fetch('/orders/{{ $order->uuid }}/payments/' + payment.uuid, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await response.json();
                                if (response.ok) {
                                    this.payments = this.payments.filter(p => p.id !== payment.id);
                                    this.order.total_paid = data.order.total_paid;
                                    this.order.remaining_amount = data.order.remaining_amount;
                                    this.order.is_fully_paid = data.order.is_fully_paid;
                                    this.showMessage('Pagamento removido');
                                }
                            } catch (error) {
                                this.showMessage('Erro ao remover pagamento', 'error');
                            }
                        },
                        { buttonText: 'Remover', type: 'danger' }
                    );
                },

                closeOrder() {
                    // Verifica se há itens no pedido
                    const activeItems = this.items.filter(i => i.status !== 'cancelled');
                    if (activeItems.length === 0) {
                        this.showMessage('Não é possível fechar um pedido sem itens.', 'error');
                        return;
                    }

                    this.showConfirm(
                        'Fechar Conta',
                        'Tem certeza que deseja fechar este pedido? Esta ação não pode ser desfeita.',
                        async () => {
                            try {
                                const response = await fetch('/orders/{{ $order->uuid }}/close', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });
                                if (response.ok) {
                                    window.location.reload();
                                } else {
                                    const data = await response.json();
                                    this.showMessage(data.message, 'error');
                                }
                            } catch (error) {
                                this.showMessage('Erro ao fechar pedido', 'error');
                            }
                        },
                        { buttonText: 'Fechar Conta', type: 'info' }
                    );
                },

                async addPayment() {
                    if (!this.paymentForm.method || !this.paymentForm.amount || this.paymentForm.amount <= 0) return;

                    this.savingPayment = true;
                    try {
                        const response = await fetch('/orders/{{ $order->uuid }}/payments', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                method: this.paymentForm.method,
                                amount: this.paymentForm.amount
                            })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.payments.push(data.payment);
                            this.order.total_paid = data.order.total_paid;
                            this.order.remaining_amount = data.order.remaining_amount;
                            this.order.is_fully_paid = data.order.is_fully_paid;
                            this.showPaymentModal = false;
                            this.paymentForm = { method: 'cash', amount: 0 };
                            this.showMessage('Pagamento registrado');
                        } else {
                            this.showMessage(data.message || 'Erro ao registrar pagamento', 'error');
                        }
                    } catch (error) {
                        this.showMessage('Erro ao registrar pagamento', 'error');
                    } finally {
                        this.savingPayment = false;
                    }
                },

                async applyDiscount() {
                    if (this.discountAmount < 0 || this.discountAmount > this.order.subtotal) return;

                    this.savingDiscount = true;
                    try {
                        const response = await fetch('/orders/{{ $order->uuid }}/discount', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                discount: this.discountAmount
                            })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.order.discount = data.order.discount;
                            this.order.service_fee = data.order.service_fee;
                            this.order.total = data.order.total;
                            this.order.remaining_amount = data.order.remaining_amount;
                            this.order.is_fully_paid = data.order.is_fully_paid;
                            this.showDiscountModal = false;
                            this.showMessage('Desconto aplicado');
                        } else {
                            this.showMessage(data.message || 'Erro ao aplicar desconto', 'error');
                        }
                    } catch (error) {
                        this.showMessage('Erro ao aplicar desconto', 'error');
                    } finally {
                        this.savingDiscount = false;
                    }
                },

                async toggleServiceFee() {
                    this.savingServiceFee = true;
                    try {
                        const response = await fetch('/orders/{{ $order->uuid }}/service-fee', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.order.service_fee = data.order.service_fee;
                            this.order.total = data.order.total;
                            this.order.remaining_amount = data.order.remaining_amount;
                            this.order.is_fully_paid = data.order.is_fully_paid;
                            this.showMessage(data.message);
                        } else {
                            this.showMessage(data.message || 'Erro ao alterar taxa de serviço', 'error');
                        }
                    } catch (error) {
                        this.showMessage('Erro ao alterar taxa de serviço', 'error');
                    } finally {
                        this.savingServiceFee = false;
                    }
                },

                async cancelOrder() {
                    this.savingCancel = true;
                    try {
                        const response = await fetch('/orders/{{ $order->uuid }}/cancel', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                reason: this.cancelReason
                            })
                        });
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            const data = await response.json();
                            this.showMessage(data.message || 'Erro ao cancelar pedido', 'error');
                        }
                    } catch (error) {
                        this.showMessage('Erro ao cancelar pedido', 'error');
                    } finally {
                        this.savingCancel = false;
                    }
                },

                async loadHistory(page = 1) {
                    this.loadingHistory = true;
                    try {
                        const response = await fetch('/orders/{{ $order->uuid }}/history?page=' + page, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.histories = data.data;
                            this.historyPagination = {
                                current_page: data.current_page,
                                last_page: data.last_page,
                                per_page: data.per_page,
                                total: data.total
                            };
                        }
                    } catch (error) {
                        console.error('Erro ao carregar histórico:', error);
                    } finally {
                        this.loadingHistory = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
