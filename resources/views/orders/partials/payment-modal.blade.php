<!-- Modal Pagamento -->
<div x-show="showPaymentModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <div x-show="showPaymentModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
         @click="showPaymentModal = false"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="showPaymentModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">

            <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Adicionar Pagamento</h3>
                    <button type="button" @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 px-6 py-4">
                <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Total do Pedido</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100" x-text="'R$ ' + order.total.toFixed(2).replace('.', ',')"></span>
                    </div>
                    <div class="flex justify-between text-sm mt-1">
                        <span class="text-gray-600 dark:text-gray-400">Já Pago</span>
                        <span class="text-green-600 dark:text-green-400" x-text="'R$ ' + order.total_paid.toFixed(2).replace('.', ',')"></span>
                    </div>
                    <div class="flex justify-between text-sm font-medium mt-1 pt-1 border-t border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-400">Restante</span>
                        <span class="text-red-600 dark:text-red-400" x-text="'R$ ' + order.remaining_amount.toFixed(2).replace('.', ',')"></span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Forma de Pagamento</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="paymentForm.method = 'cash'"
                                    :class="paymentForm.method === 'cash' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="p-3 border rounded-lg text-sm font-medium transition-colors flex flex-col items-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Dinheiro
                            </button>
                            <button type="button" @click="paymentForm.method = 'credit_card'"
                                    :class="paymentForm.method === 'credit_card' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="p-3 border rounded-lg text-sm font-medium transition-colors flex flex-col items-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Crédito
                            </button>
                            <button type="button" @click="paymentForm.method = 'debit_card'"
                                    :class="paymentForm.method === 'debit_card' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="p-3 border rounded-lg text-sm font-medium transition-colors flex flex-col items-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Débito
                            </button>
                            <button type="button" @click="paymentForm.method = 'pix'"
                                    :class="paymentForm.method === 'pix' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="p-3 border rounded-lg text-sm font-medium transition-colors flex flex-col items-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                PIX
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">R$</span>
                            <input type="text"
                                   inputmode="decimal"
                                   x-ref="paymentAmountInput"
                                   :value="formatCurrency(paymentForm.amount)"
                                   @input="paymentForm.amount = maskCurrency($event)"
                                   @focus="$event.target.select()"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button @click="paymentForm.amount = order.remaining_amount; $refs.paymentAmountInput.value = formatCurrency(order.remaining_amount)"
                                class="mt-1 text-xs text-blue-600 dark:text-blue-400 hover:underline">
                            Preencher valor restante
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end gap-3">
                <button type="button"
                        @click="showPaymentModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                    Cancelar
                </button>
                <button type="button"
                        @click="addPayment()"
                        :disabled="savingPayment || !paymentForm.method || !paymentForm.amount || paymentForm.amount <= 0"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:bg-green-400 rounded-md transition-colors duration-200">
                    <span x-text="savingPayment ? 'Salvando...' : 'Confirmar Pagamento'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
