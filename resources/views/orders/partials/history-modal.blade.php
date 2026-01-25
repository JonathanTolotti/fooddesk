<!-- Modal Histórico -->
<div x-show="showHistoryModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <div x-show="showHistoryModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
         @click="showHistoryModal = false"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="showHistoryModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl flex flex-col"
             style="height: 500px;">

            <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Histórico do Pedido</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-show="historyPagination.total > 0">
                            <span x-text="historyPagination.total"></span> registros
                        </p>
                    </div>
                    <button type="button" @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 px-6 py-4 flex-1 overflow-y-auto custom-scrollbar">
                <!-- Loading -->
                <div x-show="loadingHistory" class="flex justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <!-- Empty State -->
                <div x-show="!loadingHistory && histories.length === 0" class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nenhum histórico encontrado.</p>
                </div>

                <!-- History List -->
                <div x-show="!loadingHistory && histories.length > 0" class="space-y-3">
                    <template x-for="history in histories" :key="history.id">
                        <div class="flex gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="history.event_label"></p>
                                <p x-show="history.description" class="text-sm text-gray-600 dark:text-gray-400" x-text="history.description"></p>
                                <p x-show="history.field && !history.description" class="text-sm text-gray-600 dark:text-gray-400">
                                    <span x-text="history.field_label"></span>:
                                    <span class="text-red-500 line-through" x-text="history.old_value"></span>
                                    <span class="mx-1">&rarr;</span>
                                    <span class="text-green-600 dark:text-green-400" x-text="history.new_value"></span>
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="history.created_at"></span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">•</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="history.user_name"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex items-center justify-between flex-shrink-0">
                <div class="text-sm text-gray-500 dark:text-gray-400" x-show="historyPagination.total > 0">
                    Página <span x-text="historyPagination.current_page"></span> de <span x-text="historyPagination.last_page"></span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button"
                            @click="loadHistory(historyPagination.current_page - 1)"
                            :disabled="historyPagination.current_page <= 1 || loadingHistory"
                            class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                        Anterior
                    </button>
                    <button type="button"
                            @click="loadHistory(historyPagination.current_page + 1)"
                            :disabled="historyPagination.current_page >= historyPagination.last_page || loadingHistory"
                            class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                        Próxima
                    </button>
                    <button type="button"
                            @click="showHistoryModal = false"
                            class="px-4 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
