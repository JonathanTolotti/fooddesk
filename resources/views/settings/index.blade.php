<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Configurações do Sistema
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div x-data="settingsManager()" x-init="init()">
                <form @submit.prevent="saveSettings">
                    <!-- Category Tabs -->
                    <div class="mb-6">
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                @foreach($categories as $key => $label)
                                    <button type="button"
                                            @click="activeTab = '{{ $key }}'"
                                            :class="activeTab === '{{ $key }}'
                                                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </nav>
                        </div>
                    </div>

                    <!-- Settings Panels -->
                    @foreach($categories as $categoryKey => $categoryLabel)
                        <div x-show="activeTab === '{{ $categoryKey }}'" x-cloak
                             class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-6">
                                    {{ $categoryLabel }}
                                </h3>

                                <div class="space-y-6">
                                    @if(isset($groupedSettings[$categoryKey]))
                                        @foreach($groupedSettings[$categoryKey] as $setting)
                                            <div class="flex flex-col">
                                                <div class="flex items-center justify-between">
                                                    <label for="setting_{{ $setting->key }}"
                                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {{ $setting->label }}
                                                    </label>
                                                    @if($setting->histories()->count() > 0)
                                                        <button type="button"
                                                                @click="showHistory('{{ $setting->uuid }}')"
                                                                class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                                            Histórico
                                                        </button>
                                                    @endif
                                                </div>

                                                @if($setting->description)
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $setting->description }}
                                                    </p>
                                                @endif

                                                <div class="mt-2">
                                                    @switch($setting->type)
                                                        @case('boolean')
                                                            <label class="relative inline-flex items-center cursor-pointer">
                                                                <input type="checkbox"
                                                                       id="setting_{{ $setting->key }}"
                                                                       name="settings[{{ $setting->key }}]"
                                                                       x-model="formData['{{ $setting->key }}']"
                                                                       class="sr-only peer">
                                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                                            </label>
                                                            @break

                                                        @case('integer')
                                                            <input type="number"
                                                                   id="setting_{{ $setting->key }}"
                                                                   name="settings[{{ $setting->key }}]"
                                                                   x-model="formData['{{ $setting->key }}']"
                                                                   class="mt-1 block w-full sm:w-32 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm">
                                                            @break

                                                        @case('float')
                                                            <input type="number"
                                                                   id="setting_{{ $setting->key }}"
                                                                   name="settings[{{ $setting->key }}]"
                                                                   x-model="formData['{{ $setting->key }}']"
                                                                   step="0.01"
                                                                   class="mt-1 block w-full sm:w-32 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm">
                                                            @break

                                                        @case('time')
                                                            <input type="time"
                                                                   id="setting_{{ $setting->key }}"
                                                                   name="settings[{{ $setting->key }}]"
                                                                   x-model="formData['{{ $setting->key }}']"
                                                                   class="mt-1 block w-full sm:w-40 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm">
                                                            @break

                                                        @case('json')
                                                            @if($setting->key === 'operating_days')
                                                                <div class="flex flex-wrap gap-2 mt-1">
                                                                    @php
                                                                        $days = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                                                                    @endphp
                                                                    @foreach($days as $index => $day)
                                                                        <label class="inline-flex items-center">
                                                                            <input type="checkbox"
                                                                                   name="settings[operating_days][]"
                                                                                   value="{{ $index }}"
                                                                                   :checked="formData.operating_days && formData.operating_days.includes({{ $index }})"
                                                                                   @change="toggleDay({{ $index }})"
                                                                                   class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700">
                                                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $day }}</span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                            @break

                                                        @default
                                                            @if($setting->key === 'service_fee_type')
                                                                <select id="setting_{{ $setting->key }}"
                                                                        name="settings[{{ $setting->key }}]"
                                                                        x-model="formData['{{ $setting->key }}']"
                                                                        class="mt-1 block w-full sm:w-48 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm">
                                                                    <option value="percentage">Percentual</option>
                                                                    <option value="fixed">Valor Fixo</option>
                                                                </select>
                                                            @else
                                                                <input type="text"
                                                                       id="setting_{{ $setting->key }}"
                                                                       name="settings[{{ $setting->key }}]"
                                                                       x-model="formData['{{ $setting->key }}']"
                                                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm">
                                                            @endif
                                                    @endswitch
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Save Button -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                                :disabled="saving"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150">
                            <svg x-show="saving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="saving ? 'Salvando...' : 'Salvar Configurações'"></span>
                        </button>
                    </div>
                </form>

                <!-- Success Message -->
                <div x-show="showSuccess"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-2"
                     class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
                    Configurações salvas com sucesso!
                </div>

                <!-- Error Message -->
                <div x-show="errorMessage"
                     x-transition
                     class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
                    <span x-text="errorMessage"></span>
                </div>

                <!-- History Modal -->
                <div x-show="historyModal.open"
                     x-cloak
                     class="fixed inset-0 z-50 overflow-y-auto"
                     aria-labelledby="modal-title"
                     role="dialog"
                     aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div x-show="historyModal.open"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click="historyModal.open = false"
                             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <div x-show="historyModal.open"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100" x-text="'Histórico: ' + historyModal.settingLabel"></h3>
                                    <button @click="historyModal.open = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                    <template x-if="historyModal.loading">
                                        <div class="flex justify-center py-8">
                                            <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </template>

                                    <template x-if="!historyModal.loading && historyModal.histories.length === 0">
                                        <p class="text-center text-gray-500 dark:text-gray-400 py-8">Nenhum histórico encontrado.</p>
                                    </template>

                                    <template x-if="!historyModal.loading && historyModal.histories.length > 0">
                                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                            <template x-for="history in historyModal.histories" :key="history.id">
                                                <div class="py-3">
                                                    <div class="flex items-center justify-between text-sm">
                                                        <span class="font-medium text-gray-900 dark:text-gray-100" x-text="history.event_label"></span>
                                                        <span class="text-gray-500 dark:text-gray-400" x-text="history.created_at"></span>
                                                    </div>
                                                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                        <template x-if="history.event === 'updated'">
                                                            <span>
                                                                <span class="text-red-500" x-text="history.old_value"></span>
                                                                <span class="mx-1">&rarr;</span>
                                                                <span class="text-green-500" x-text="history.new_value"></span>
                                                            </span>
                                                        </template>
                                                        <template x-if="history.event === 'created'">
                                                            <span class="text-green-500" x-text="history.new_value || 'Valor inicial'"></span>
                                                        </template>
                                                    </div>
                                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        por <span x-text="history.user_name"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                <!-- Pagination -->
                                <template x-if="!historyModal.loading && historyModal.pagination.total > historyModal.pagination.per_page">
                                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <button @click="loadHistory(historyModal.settingUuid, historyModal.pagination.current_page - 1)"
                                                :disabled="historyModal.pagination.current_page === 1"
                                                class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Anterior
                                        </button>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            Página <span x-text="historyModal.pagination.current_page"></span> de <span x-text="historyModal.pagination.last_page"></span>
                                        </span>
                                        <button @click="loadHistory(historyModal.settingUuid, historyModal.pagination.current_page + 1)"
                                                :disabled="!historyModal.pagination.has_more"
                                                class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Próxima
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function settingsManager() {
            return {
                activeTab: 'establishment',
                saving: false,
                showSuccess: false,
                errorMessage: '',
                formData: {
                    @foreach($groupedSettings as $category => $settings)
                        @foreach($settings as $setting)
                            @if($setting->type === 'boolean')
                                '{{ $setting->key }}': {{ $setting->typed_value ? 'true' : 'false' }},
                            @elseif($setting->type === 'json')
                                '{{ $setting->key }}': {!! json_encode($setting->typed_value) !!},
                            @else
                                '{{ $setting->key }}': '{{ addslashes($setting->typed_value ?? '') }}',
                            @endif
                        @endforeach
                    @endforeach
                },
                historyModal: {
                    open: false,
                    loading: false,
                    settingUuid: '',
                    settingLabel: '',
                    histories: [],
                    pagination: {
                        current_page: 1,
                        last_page: 1,
                        per_page: 10,
                        total: 0,
                        has_more: false
                    }
                },

                init() {
                    // Initialize operating_days if not set
                    if (!this.formData.operating_days) {
                        this.formData.operating_days = [0, 1, 2, 3, 4, 5, 6];
                    }
                },

                toggleDay(day) {
                    if (!this.formData.operating_days) {
                        this.formData.operating_days = [];
                    }

                    const index = this.formData.operating_days.indexOf(day);
                    if (index === -1) {
                        this.formData.operating_days.push(day);
                    } else {
                        this.formData.operating_days.splice(index, 1);
                    }
                    this.formData.operating_days.sort((a, b) => a - b);
                },

                async saveSettings() {
                    this.saving = true;
                    this.errorMessage = '';

                    try {
                        const response = await fetch('{{ route("settings.update") }}', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ settings: this.formData })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.showSuccess = true;
                            setTimeout(() => this.showSuccess = false, 3000);
                        } else {
                            if (data.errors) {
                                const firstError = Object.values(data.errors)[0];
                                this.errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                            } else {
                                this.errorMessage = data.message || 'Erro ao salvar configurações.';
                            }
                            setTimeout(() => this.errorMessage = '', 5000);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.errorMessage = 'Erro de conexão. Tente novamente.';
                        setTimeout(() => this.errorMessage = '', 5000);
                    } finally {
                        this.saving = false;
                    }
                },

                async showHistory(uuid) {
                    this.historyModal.open = true;
                    this.historyModal.settingUuid = uuid;
                    await this.loadHistory(uuid, 1);
                },

                async loadHistory(uuid, page = 1) {
                    this.historyModal.loading = true;

                    try {
                        const response = await fetch(`/settings/${uuid}/history?page=${page}`, {
                            headers: { 'Accept': 'application/json' }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.historyModal.settingLabel = data.setting.label;
                            this.historyModal.histories = data.histories;
                            this.historyModal.pagination = data.pagination;
                        }
                    } catch (error) {
                        console.error('Error loading history:', error);
                    } finally {
                        this.historyModal.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
