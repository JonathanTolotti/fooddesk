<!-- Modal Desconto -->
<div x-show="showDiscountModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <div x-show="showDiscountModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
         @click="showDiscountModal = false"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="showDiscountModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm">

            <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Aplicar Desconto</h3>
                    <button type="button" @click="showDiscountModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 px-6 py-4">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Subtotal do pedido: <span class="font-medium text-gray-900 dark:text-gray-100" x-text="'R$ ' + order.subtotal.toFixed(2).replace('.', ',')"></span></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor do Desconto</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">R$</span>
                        <input type="text"
                               inputmode="decimal"
                               :value="formatCurrency(discountAmount)"
                               @input="discountAmount = Math.min(maskCurrency($event), order.subtotal)"
                               @focus="$event.target.select()"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex justify-between text-sm font-medium">
                        <span class="text-gray-600 dark:text-gray-400">Novo Total</span>
                        <span class="text-gray-900 dark:text-gray-100" x-text="'R$ ' + Math.max(0, order.subtotal - discountAmount).toFixed(2).replace('.', ',')"></span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end gap-3">
                <button type="button"
                        @click="showDiscountModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                    Cancelar
                </button>
                <button type="button"
                        @click="applyDiscount()"
                        :disabled="savingDiscount"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 rounded-md transition-colors duration-200">
                    <span x-text="savingDiscount ? 'Salvando...' : 'Aplicar'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
