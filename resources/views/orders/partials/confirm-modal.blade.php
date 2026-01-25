<!-- Modal Confirmação Genérico -->
<div x-show="showConfirmModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <div x-show="showConfirmModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
         @click="showConfirmModal = false; confirmCallback = null"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="showConfirmModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">

            <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4">
                <div class="flex items-start gap-4">
                    <div :class="{
                        'bg-red-100 dark:bg-red-900/30': confirmType === 'danger',
                        'bg-green-100 dark:bg-green-900/30': confirmType === 'success',
                        'bg-blue-100 dark:bg-blue-900/30': confirmType === 'info' || confirmType === 'default'
                    }"
                         class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center">
                        <svg x-show="confirmType === 'danger'" class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <svg x-show="confirmType === 'success'" class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="confirmType === 'info' || confirmType === 'default'" class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="confirmTitle"></h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400" x-text="confirmMessage"></p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end gap-3">
                <button type="button"
                        @click="showConfirmModal = false; confirmCallback = null"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                    Cancelar
                </button>
                <button type="button"
                        @click="executeConfirm()"
                        :class="{
                            'bg-red-600 hover:bg-red-700': confirmType === 'danger',
                            'bg-green-600 hover:bg-green-700': confirmType === 'success',
                            'bg-blue-600 hover:bg-blue-700': confirmType === 'info' || confirmType === 'default'
                        }"
                        class="px-4 py-2 text-sm font-medium text-white rounded-md transition-colors duration-200">
                    <span x-text="confirmButtonText"></span>
                </button>
            </div>
        </div>
    </div>
</div>
