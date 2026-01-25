<!-- Modal Editar Item -->
<div x-data="editItemModal()"
     @open-edit-item-modal.window="openModal($event.detail)"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
         @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

            <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Editar Item</h3>
                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="loading" class="bg-white dark:bg-gray-800 px-6 py-12 text-center">
                <svg class="animate-spin h-8 w-8 mx-auto text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Carregando...</p>
            </div>

            <div x-show="!loading" class="bg-white dark:bg-gray-800 px-6 py-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                <!-- Product Info -->
                <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="font-medium text-gray-900 dark:text-gray-100" x-text="item?.product_name"></p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Quantidade: <span x-text="item?.quantity"></span> x R$ <span x-text="item?.unit_price?.toFixed(2).replace('.', ',')"></span>
                    </p>
                </div>

                <!-- Standard Ingredients (can remove) -->
                <div x-show="standardIngredients.length > 0" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remover Ingredientes</label>
                    <div class="space-y-2">
                        <template x-for="ing in standardIngredients" :key="ing.id">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" :value="ing.id" x-model="form.removed_ingredients"
                                       class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300" x-text="'Sem ' + ing.name"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Additional Ingredients (can add) -->
                <div x-show="additionalIngredients.length > 0" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adicionar Ingredientes</label>
                    <div class="space-y-2">
                        <template x-for="ing in additionalIngredients" :key="ing.id">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" :value="ing.id" x-model="form.added_ingredients"
                                           class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="'+ ' + ing.name"></span>
                                </div>
                                <span class="text-sm text-green-600 dark:text-green-400" x-text="'+R$ ' + ing.price.toFixed(2).replace('.', ',')"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                    <textarea x-model="form.notes"
                              rows="2"
                              placeholder="Ex: Bem passado, sem sal..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                </div>

                <!-- Total Preview -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span class="text-gray-900 dark:text-gray-100">Total do Item</span>
                        <span class="text-gray-900 dark:text-gray-100" x-text="'R$ ' + calculateTotal().toFixed(2).replace('.', ',')"></span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end gap-3">
                <button type="button"
                        @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                    Cancelar
                </button>
                <button type="button"
                        @click="saveItem()"
                        :disabled="saving"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 rounded-md transition-colors duration-200">
                    <span x-text="saving ? 'Salvando...' : 'Salvar Alterações'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function editItemModal() {
        return {
            open: false,
            loading: false,
            saving: false,
            item: null,
            product: null,
            standardIngredients: [],
            additionalIngredients: [],
            form: {
                removed_ingredients: [],
                added_ingredients: [],
                notes: ''
            },

            async openModal(item) {
                this.open = true;
                this.loading = true;
                this.item = item;
                this.resetForm();
                await this.loadProductData();
                this.loading = false;
            },

            resetForm() {
                this.product = null;
                this.standardIngredients = [];
                this.additionalIngredients = [];
                this.form = {
                    removed_ingredients: [],
                    added_ingredients: [],
                    notes: this.item?.notes || ''
                };
            },

            async loadProductData() {
                if (!this.item?.product_id) return;

                try {
                    // Load product with ingredients
                    const response = await fetch('{{ route('products.filter') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: 'active', per_page: 100 })
                    });
                    const data = await response.json();
                    this.product = data.products.find(p => p.id === this.item.product_id);

                    if (this.product) {
                        // Get standard ingredients (can be removed)
                        this.standardIngredients = (this.product.ingredients || [])
                            .filter(i => i.type === 'standard')
                            .map(i => ({ id: i.id, name: i.name }));

                        // Get additional ingredients (can be added)
                        this.additionalIngredients = (this.product.ingredients || [])
                            .filter(i => i.type === 'additional')
                            .map(i => ({ id: i.id, name: i.name, price: parseFloat(i.additional_price) || 0 }));

                        // Pre-select current customizations
                        this.form.removed_ingredients = (this.item.customizations || [])
                            .filter(c => c.action === 'removed')
                            .map(c => {
                                const ing = this.standardIngredients.find(i => i.name === c.ingredient_name);
                                return ing ? ing.id : null;
                            })
                            .filter(id => id !== null);

                        this.form.added_ingredients = (this.item.customizations || [])
                            .filter(c => c.action === 'added')
                            .map(c => {
                                const ing = this.additionalIngredients.find(i => i.name === c.ingredient_name);
                                return ing ? ing.id : null;
                            })
                            .filter(id => id !== null);
                    }
                } catch (error) {
                    console.error('Erro ao carregar dados do produto:', error);
                }
            },

            calculateTotal() {
                if (!this.item) return 0;

                let additionsPrice = 0;

                // Add additional ingredients price
                this.form.added_ingredients.forEach(ingId => {
                    const ing = this.additionalIngredients.find(i => i.id == ingId);
                    if (ing) additionsPrice += parseFloat(ing.price) || 0;
                });

                return ((parseFloat(this.item.unit_price) || 0) + additionsPrice) * this.item.quantity;
            },

            async saveItem() {
                this.saving = true;

                try {
                    // Build removed ingredients data
                    const removedIngredients = this.form.removed_ingredients.map(id => {
                        const ing = this.standardIngredients.find(i => i.id == id);
                        return { id: ing.id, name: ing.name };
                    });

                    // Build added ingredients data
                    const addedIngredients = this.form.added_ingredients.map(id => {
                        const ing = this.additionalIngredients.find(i => i.id == id);
                        return { id: ing.id, name: ing.name, price: ing.price };
                    });

                    const response = await fetch('/orders/{{ $order->uuid }}/items/' + this.item.uuid + '/ingredients', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            removed_ingredients: removedIngredients,
                            added_ingredients: addedIngredients,
                            notes: this.form.notes
                        })
                    });

                    if (response.ok) {
                        this.open = false;
                        window.location.reload();
                    } else {
                        const data = await response.json();
                        window.dispatchEvent(new CustomEvent('show-message', {
                            detail: { text: data.message || 'Erro ao salvar alterações', type: 'error' }
                        }));
                    }
                } catch (error) {
                    console.error('Erro ao salvar item:', error);
                    window.dispatchEvent(new CustomEvent('show-message', {
                        detail: { text: 'Erro ao salvar alterações', type: 'error' }
                    }));
                } finally {
                    this.saving = false;
                }
            }
        }
    }
</script>
