<!-- Modal Adicionar Item -->
<div x-data="addItemModal()"
     @open-add-item-modal.window="openModal()"
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
             class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">

            <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Adicionar Item</h3>
                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 px-6 py-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                <!-- Search -->
                <div class="mb-4">
                    <input type="text" x-model="search"
                           @input="filterProducts()"
                           placeholder="Buscar produto..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Categories Tabs -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <button @click="selectedCategory = null; filterProducts()"
                            :class="selectedCategory === null ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                            class="px-3 py-1.5 text-sm font-medium rounded-full transition-colors">
                        Todos
                    </button>
                    @foreach($categories as $category)
                        <button @click="selectedCategory = {{ $category->id }}; filterProducts()"
                                :class="selectedCategory === {{ $category->id }} ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                class="px-3 py-1.5 text-sm font-medium rounded-full transition-colors">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>

                <!-- Product Selection -->
                <div x-show="!selectedProduct" class="space-y-2">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="selectProduct(product)"
                             class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100" x-text="product.name"></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="product.description"></p>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-gray-100" x-text="'R$ ' + product.price.toFixed(2).replace('.', ',')"></span>
                            </div>
                        </div>
                    </template>
                    <p x-show="filteredProducts.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                        Nenhum produto encontrado
                    </p>
                </div>

                <!-- Product Configuration -->
                <div x-show="selectedProduct" class="space-y-4">
                    <div class="flex items-center gap-2 mb-4">
                        <button @click="selectedProduct = null" class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </button>
                        <h4 class="font-medium text-gray-900 dark:text-gray-100" x-text="selectedProduct?.name"></h4>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantidade</label>
                        <div class="flex items-center gap-3">
                            <button @click="form.quantity = Math.max(1, form.quantity - 1)"
                                    class="w-10 h-10 rounded-full border border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <span class="text-xl font-bold text-gray-900 dark:text-gray-100 w-12 text-center" x-text="form.quantity"></span>
                            <button @click="form.quantity++"
                                    class="w-10 h-10 rounded-full border border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Standard Ingredients (can remove) -->
                    <div x-show="standardIngredients.length > 0">
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
                    <div x-show="additionalIngredients.length > 0">
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                        <textarea x-model="form.notes"
                                  rows="2"
                                  placeholder="Ex: Bem passado, sem sal..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                    </div>

                    <!-- Total -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between items-center text-lg font-bold">
                            <span class="text-gray-900 dark:text-gray-100">Total</span>
                            <span class="text-gray-900 dark:text-gray-100" x-text="'R$ ' + calculateTotal().toFixed(2).replace('.', ',')"></span>
                        </div>
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
                        @click="addItem()"
                        :disabled="!selectedProduct || saving"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 rounded-md transition-colors duration-200">
                    <span x-text="saving ? 'Adicionando...' : 'Adicionar ao Pedido'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function addItemModal() {
        return {
            open: false,
            saving: false,
            search: '',
            selectedCategory: null,
            selectedProduct: null,
            products: [],
            filteredProducts: [],
            standardIngredients: [],
            additionalIngredients: [],
            form: {
                quantity: 1,
                removed_ingredients: [],
                added_ingredients: [],
                notes: ''
            },

            async openModal() {
                this.open = true;
                this.resetForm();
                await this.loadProducts();
            },

            resetForm() {
                this.search = '';
                this.selectedCategory = null;
                this.selectedProduct = null;
                this.standardIngredients = [];
                this.additionalIngredients = [];
                this.form = {
                    quantity: 1,
                    removed_ingredients: [],
                    added_ingredients: [],
                    notes: ''
                };
            },

            async loadProducts() {
                try {
                    const response = await fetch('{{ route('orders.products') }}', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    this.products = data.products;
                    this.filterProducts();
                } catch (error) {
                    console.error('Erro ao carregar produtos:', error);
                }
            },

            filterProducts() {
                this.filteredProducts = this.products.filter(p => {
                    const matchesSearch = !this.search ||
                        p.name.toLowerCase().includes(this.search.toLowerCase());
                    const matchesCategory = !this.selectedCategory ||
                        p.category_id === this.selectedCategory;
                    return matchesSearch && matchesCategory;
                });
            },

            selectProduct(product) {
                this.selectedProduct = product;
                this.loadProductIngredients(product);
            },

            async loadProductIngredients(product) {
                // Get ingredients from the product data
                this.standardIngredients = (product.ingredients || [])
                    .filter(i => i.type === 'standard')
                    .map(i => ({ id: i.id, name: i.name }));

                this.additionalIngredients = (product.ingredients || [])
                    .filter(i => i.type === 'additional')
                    .map(i => ({ id: i.id, name: i.name, price: parseFloat(i.additional_price) || 0 }));
            },

            calculateTotal() {
                if (!this.selectedProduct) return 0;

                let total = parseFloat(this.selectedProduct.price) || 0;

                // Add additional ingredients price
                this.form.added_ingredients.forEach(ingId => {
                    const ing = this.additionalIngredients.find(i => i.id == ingId);
                    if (ing) total += parseFloat(ing.price) || 0;
                });

                return total * this.form.quantity;
            },

            async addItem() {
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

                    const response = await fetch('/orders/{{ $order->uuid }}/items', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: this.selectedProduct.id,
                            quantity: this.form.quantity,
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
                            detail: { text: data.message || 'Erro ao adicionar item', type: 'error' }
                        }));
                    }
                } catch (error) {
                    console.error('Erro ao adicionar item:', error);
                    window.dispatchEvent(new CustomEvent('show-message', {
                        detail: { text: 'Erro ao adicionar item', type: 'error' }
                    }));
                } finally {
                    this.saving = false;
                }
            }
        }
    }
</script>
