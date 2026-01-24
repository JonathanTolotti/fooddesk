<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Produtos</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerencie os produtos do cardápio</p>
            </div>
            <button type="button"
                    @click="$dispatch('open-product-modal', { mode: 'create' })"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Produto
            </button>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="productManager()"
         x-init="init()">

        <!-- Success Message -->
        <div x-show="successMessage"
             x-transition
             x-cloak
             class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg flex items-center justify-between">
            <span x-text="successMessage"></span>
            <button type="button" @click="successMessage = ''" class="text-green-500 hover:text-green-700 dark:hover:text-green-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                    <input type="text" x-model="filters.search"
                           @keydown.enter="fetchProducts()"
                           placeholder="Nome do produto..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoria</label>
                    <select x-model="filters.category_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select x-model="filters.status"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="active">Ativo</option>
                        <option value="inactive">Inativo</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="button" @click="fetchProducts()" :disabled="loading"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-md transition-colors duration-200 flex items-center justify-center">
                        <template x-if="loading">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!loading">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Buscando...' : 'Pesquisar'"></span>
                    </button>
                    <button type="button" @click="clearFilters()"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        Limpar
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Produto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoria</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Preço</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="product in products" :key="product.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <td class="px-6 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <template x-if="product.image_url">
                                                <img class="h-10 w-10 rounded-lg object-cover" :src="product.image_url" :alt="product.name">
                                            </template>
                                            <template x-if="!product.image_url">
                                                <div class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="product.name"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs" x-text="product.description || ''"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300" x-text="product.category_name"></span>
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap text-right">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="product.price_formatted"></div>
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap text-center">
                                    <span x-show="product.is_active" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        Ativo
                                    </span>
                                    <span x-show="!product.is_active" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                        Inativo
                                    </span>
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button"
                                                @click="$dispatch('open-product-modal', { mode: 'edit', product: product })"
                                                class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/50 rounded-lg transition-colors duration-150"
                                                title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                                @click="$dispatch('open-history-modal', { product: product })"
                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-150"
                                                title="Histórico">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                                @click="toggleStatus(product)"
                                                :class="product.is_active ? 'text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/50' : 'text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900/50'"
                                                class="p-2 rounded-lg transition-colors duration-150"
                                                :title="product.is_active ? 'Inativar' : 'Ativar'">
                                            <svg x-show="product.is_active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            <svg x-show="!product.is_active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="products.length === 0 && !loading">
                            <td colspan="5" class="text-center">
                                <div class="py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum produto encontrado</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tente ajustar os filtros ou cadastre um novo produto.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="pagination.total > 0" class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Mostrando
                        <span class="font-medium" x-text="(pagination.current_page - 1) * pagination.per_page + 1"></span>
                        a
                        <span class="font-medium" x-text="Math.min(pagination.current_page * pagination.per_page, pagination.total)"></span>
                        de
                        <span class="font-medium" x-text="pagination.total"></span>
                        resultados
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="goToPage(pagination.current_page - 1)"
                                :disabled="pagination.current_page <= 1 || loading"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            Anterior
                        </button>
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            Página <span x-text="pagination.current_page"></span> de <span x-text="pagination.last_page"></span>
                        </span>
                        <button type="button"
                                @click="goToPage(pagination.current_page + 1)"
                                :disabled="pagination.current_page >= pagination.last_page || loading"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            Próxima
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Criar/Editar Produto -->
    <div x-data="{
            open: false,
            mode: 'create',
            saving: false,
            form: {
                uuid: null,
                name: '',
                description: '',
                price: '',
                category_id: '',
                is_active: true,
                image: null,
                image_preview: null,
                remove_image: false,
                ingredients: []
            },
            errors: {},
            categories: @js($categories),
            availableIngredients: @js($ingredients),
            selectedIngredientId: '',
            ingredientSearch: '',
            showIngredientDropdown: false,
            get filteredIngredients() {
                const available = this.availableIngredients.filter(i => !this.form.ingredients.find(fi => fi.id === i.id));
                if (!this.ingredientSearch) return available;
                const search = this.ingredientSearch.toLowerCase();
                return available.filter(i => i.name.toLowerCase().includes(search));
            },
            get title() {
                return this.mode === 'create' ? 'Novo Produto' : 'Editar Produto';
            },
            get buttonText() {
                return this.mode === 'create' ? 'Criar Produto' : 'Salvar Alterações';
            },
            resetForm() {
                this.form = {
                    uuid: null,
                    name: '',
                    description: '',
                    price: '',
                    category_id: '',
                    is_active: true,
                    image: null,
                    image_preview: null,
                    remove_image: false,
                    ingredients: []
                };
                this.errors = {};
                this.selectedIngredientId = '';
                this.ingredientSearch = '';
                this.showIngredientDropdown = false;
                if (this.$refs.imageInput) {
                    this.$refs.imageInput.value = '';
                }
            },
            addIngredient(ingredient) {
                if (!ingredient) return;
                if (this.form.ingredients.find(i => i.id === ingredient.id)) {
                    return;
                }
                this.form.ingredients.push({
                    id: ingredient.id,
                    name: ingredient.name,
                    type: 'standard',
                    additional_price: ''
                });
                this.ingredientSearch = '';
                this.showIngredientDropdown = false;
            },
            removeIngredient(index) {
                this.form.ingredients.splice(index, 1);
            },
            getTypeLabel(type) {
                const labels = { base: 'Base', standard: 'Padrão', additional: 'Adicional' };
                return labels[type] || type;
            },
            formatAdditionalPrice(value) {
                let num = value.replace(/\D/g, '');
                num = (parseInt(num) / 100).toFixed(2);
                if (isNaN(num) || num === '0.00') return '';
                return num.replace('.', ',');
            },
            handleImageChange(event) {
                const file = event.target.files[0];
                if (file) {
                    this.form.image = file;
                    this.form.remove_image = false;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.form.image_preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },
            removeImage() {
                this.form.image = null;
                this.form.image_preview = null;
                this.form.remove_image = true;
                if (this.$refs.imageInput) {
                    this.$refs.imageInput.value = '';
                }
            },
            formatPrice(value) {
                // Remove tudo que não for número
                let num = value.replace(/\D/g, '');
                // Converte para centavos
                num = (parseInt(num) / 100).toFixed(2);
                if (isNaN(num) || num === '0.00') return '';
                return num.replace('.', ',');
            },
            async save() {
                this.errors = {};
                this.saving = true;

                try {
                    const formData = new FormData();
                    formData.append('name', this.form.name);
                    formData.append('description', this.form.description || '');
                    formData.append('price', this.form.price.replace(',', '.'));
                    formData.append('category_id', this.form.category_id);
                    formData.append('is_active', this.form.is_active ? '1' : '0');

                    if (this.form.image) {
                        formData.append('image', this.form.image);
                    }
                    if (this.form.remove_image) {
                        formData.append('remove_image', '1');
                    }

                    // Add ingredients
                    this.form.ingredients.forEach((ing, index) => {
                        formData.append(`ingredients[${index}][id]`, ing.id);
                        formData.append(`ingredients[${index}][type]`, ing.type);
                        if (ing.type === 'additional' && ing.additional_price) {
                            formData.append(`ingredients[${index}][additional_price]`, ing.additional_price.replace(',', '.'));
                        }
                    });

                    const url = this.mode === 'create'
                        ? '{{ route('products.store') }}'
                        : '/products/' + this.form.uuid;

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            this.errors = data.errors;
                        }
                        return;
                    }

                    this.open = false;
                    this.resetForm();
                    window.dispatchEvent(new CustomEvent('product-saved', { detail: data }));
                } catch (error) {
                    console.error('Erro ao salvar produto:', error);
                } finally {
                    this.saving = false;
                }
            }
         }"
         @open-product-modal.window="
            open = true;
            mode = $event.detail.mode;
            selectedIngredientId = '';
            ingredientSearch = '';
            showIngredientDropdown = false;
            if (mode === 'edit' && $event.detail.product) {
                form = {
                    uuid: $event.detail.product.uuid,
                    name: $event.detail.product.name,
                    description: $event.detail.product.description || '',
                    price: parseFloat($event.detail.product.price).toFixed(2).replace('.', ','),
                    category_id: $event.detail.product.category_id,
                    is_active: $event.detail.product.is_active,
                    image: null,
                    image_preview: $event.detail.product.image_url,
                    remove_image: false,
                    ingredients: ($event.detail.product.ingredients || []).map(ing => ({
                        id: ing.id,
                        name: ing.name,
                        type: ing.type,
                        additional_price: ing.additional_price ? parseFloat(ing.additional_price).toFixed(2).replace('.', ',') : ''
                    }))
                };
            } else {
                resetForm();
            }
         "
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
             @click="open = false; resetForm()"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <!-- Header -->
                <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="title"></h3>
                        <button type="button" @click="open = false; resetForm()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white dark:bg-gray-800 px-6 py-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div class="space-y-4">
                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nome <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="form.name"
                                   placeholder="Ex: X-Burger, Coca-Cola 350ml..."
                                   class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   :class="errors.name ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'">
                            <p x-show="errors.name" x-text="errors.name?.[0]" class="mt-1 text-sm text-red-500"></p>
                        </div>

                        <!-- Descrição -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Descrição
                            </label>
                            <textarea x-model="form.description"
                                      rows="2"
                                      placeholder="Descrição do produto..."
                                      class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      :class="errors.description ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'"></textarea>
                            <p x-show="errors.description" x-text="errors.description?.[0]" class="mt-1 text-sm text-red-500"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Preço -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Preço <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">R$</span>
                                    <input type="text" x-model="form.price"
                                           @input="form.price = formatPrice($event.target.value)"
                                           placeholder="0,00"
                                           class="w-full pl-10 pr-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           :class="errors.price ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'">
                                </div>
                                <p x-show="errors.price" x-text="errors.price?.[0]" class="mt-1 text-sm text-red-500"></p>
                            </div>

                            <!-- Categoria -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Categoria <span class="text-red-500">*</span>
                                </label>
                                <select x-model="form.category_id"
                                        class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="errors.category_id ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'">
                                    <option value="">Selecione...</option>
                                    <template x-for="cat in categories" :key="cat.id">
                                        <option :value="cat.id" x-text="cat.name"></option>
                                    </template>
                                </select>
                                <p x-show="errors.category_id" x-text="errors.category_id?.[0]" class="mt-1 text-sm text-red-500"></p>
                            </div>
                        </div>

                        <!-- Imagem -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Imagem
                            </label>
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0">
                                    <template x-if="form.image_preview">
                                        <div class="relative">
                                            <img :src="form.image_preview" class="h-20 w-20 rounded-lg object-cover">
                                            <button type="button" @click="removeImage()"
                                                    class="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!form.image_preview">
                                        <div class="h-20 w-20 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <input type="file" x-ref="imageInput"
                                           @change="handleImageChange($event)"
                                           accept="image/jpeg,image/png,image/jpg,image/webp"
                                           class="hidden">
                                    <button type="button" @click="$refs.imageInput.click()"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500">
                                        Selecionar imagem
                                    </button>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG ou WEBP até 2MB</p>
                                </div>
                            </div>
                            <p x-show="errors.image" x-text="errors.image?.[0]" class="mt-1 text-sm text-red-500"></p>
                        </div>

                        <!-- Ingredientes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Ingredientes
                            </label>

                            <!-- Adicionar ingrediente (com pesquisa) -->
                            <div class="relative mb-3" @click.outside="showIngredientDropdown = false">
                                <div class="relative">
                                    <input type="text"
                                           x-model="ingredientSearch"
                                           @focus="showIngredientDropdown = true"
                                           @keydown.escape="showIngredientDropdown = false"
                                           @keydown.enter.prevent="if (filteredIngredients.length === 1) addIngredient(filteredIngredients[0])"
                                           placeholder="Pesquisar ingrediente..."
                                           class="w-full px-3 py-2 pl-9 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>

                                <!-- Dropdown -->
                                <div x-show="showIngredientDropdown && filteredIngredients.length > 0"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg custom-scrollbar">
                                    <template x-for="ing in filteredIngredients" :key="ing.id">
                                        <button type="button"
                                                @click="addIngredient(ing)"
                                                class="w-full px-3 py-2 text-left text-sm text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-blue-900/30 flex items-center gap-2 transition-colors">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <span x-text="ing.name"></span>
                                        </button>
                                    </template>
                                </div>

                                <!-- Mensagem quando não encontrar -->
                                <div x-show="showIngredientDropdown && ingredientSearch && filteredIngredients.length === 0"
                                     class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg">
                                    <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 italic">
                                        Nenhum ingrediente encontrado
                                    </div>
                                </div>
                            </div>

                            <!-- Lista de ingredientes -->
                            <div x-show="form.ingredients.length > 0" class="border border-gray-200 dark:border-gray-600 rounded-md overflow-hidden">
                                <template x-for="(ing, index) in form.ingredients" :key="ing.id">
                                    <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 last:border-b-0">
                                        <!-- Nome -->
                                        <span class="flex-1 text-sm text-gray-900 dark:text-gray-100 font-medium" x-text="ing.name"></span>

                                        <!-- Tipo -->
                                        <select x-model="ing.type"
                                                class="w-28 px-2 py-1 text-xs border border-gray-300 dark:border-gray-500 rounded bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <option value="base">Base</option>
                                            <option value="standard">Padrão</option>
                                            <option value="additional">Adicional</option>
                                        </select>

                                        <!-- Preço adicional (só para tipo additional) -->
                                        <div x-show="ing.type === 'additional'" class="flex items-center gap-1">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">R$</span>
                                            <input type="text"
                                                   x-model="ing.additional_price"
                                                   @input="ing.additional_price = formatAdditionalPrice($event.target.value)"
                                                   placeholder="0,00"
                                                   class="w-16 px-2 py-1 text-xs border border-gray-300 dark:border-gray-500 rounded bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        </div>

                                        <!-- Remover -->
                                        <button type="button"
                                                @click="removeIngredient(index)"
                                                class="p-1 text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <!-- Legenda -->
                            <div x-show="form.ingredients.length > 0" class="mt-2 text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
                                <p><span class="font-medium">Base:</span> cliente não pode remover</p>
                                <p><span class="font-medium">Padrão:</span> vem no produto, pode remover</p>
                                <p><span class="font-medium">Adicional:</span> cliente pode adicionar (com preço)</p>
                            </div>

                            <!-- Mensagem vazia -->
                            <p x-show="form.ingredients.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic">
                                Nenhum ingrediente adicionado
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300" x-text="form.is_active ? 'Ativo' : 'Inativo'"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end gap-3">
                    <button type="button"
                            @click="open = false; resetForm()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="button"
                            @click="save()"
                            :disabled="saving"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 rounded-md transition-colors duration-200 flex items-center">
                        <svg x-show="saving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="saving ? 'Salvando...' : buttonText"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Histórico -->
    <div x-data="historyModal()"
         @open-history-modal.window="openModal($event.detail.product)"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="history-modal-title"
         role="dialog"
         aria-modal="true">

        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
             @click="open = false"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl flex flex-col"
                 style="height: 500px;">

                <!-- Header -->
                <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Histórico de Alterações</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <span x-text="productName"></span>
                                <span x-show="pagination.total > 0" class="text-xs">(<span x-text="pagination.total"></span> registros)</span>
                            </p>
                        </div>
                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="bg-white dark:bg-gray-800 px-6 py-4 flex-1 overflow-y-auto custom-scrollbar">
                    <!-- Loading -->
                    <div x-show="loading" class="flex justify-center py-8">
                        <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!loading && histories.length === 0" class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nenhum histórico encontrado.</p>
                    </div>

                    <!-- History Table -->
                    <div x-show="!loading && histories.length > 0">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Usuário</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Campo</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">De</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Para</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="history in histories" :key="history.id">
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap" x-text="history.created_at"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100" x-text="history.user_name"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100" x-text="history.event === 'created' ? 'Criação' : (history.event === 'deleted' ? 'Exclusão' : history.field_label)"></td>
                                        <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400" x-text="history.old_value"></td>
                                        <td class="px-3 py-2 text-sm text-blue-600 dark:text-blue-400 font-medium" x-text="history.new_value"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer with Pagination -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex items-center justify-between flex-shrink-0">
                    <div class="text-sm text-gray-500 dark:text-gray-400" x-show="pagination.total > 0">
                        Página <span x-text="pagination.current_page"></span> de <span x-text="pagination.last_page"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="goToPage(pagination.current_page - 1)"
                                :disabled="pagination.current_page <= 1 || loading"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            Anterior
                        </button>
                        <button type="button"
                                @click="goToPage(pagination.current_page + 1)"
                                :disabled="pagination.current_page >= pagination.last_page || loading"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            Próxima
                        </button>
                        <button type="button"
                                @click="open = false"
                                class="px-4 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function historyModal() {
            return {
                open: false,
                loading: false,
                productUuid: null,
                productName: '',
                histories: [],
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0
                },
                openModal(product) {
                    this.productUuid = product.uuid;
                    this.productName = product.name;
                    this.histories = [];
                    this.pagination = {
                        current_page: 1,
                        last_page: 1,
                        per_page: 10,
                        total: 0
                    };
                    this.open = true;
                    this.loadHistory(1);
                },
                async loadHistory(page) {
                    this.loading = true;
                    try {
                        const response = await fetch('/products/' + this.productUuid + '/history?page=' + page + '&per_page=' + this.pagination.per_page, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        this.histories = data.histories;
                        this.pagination = data.pagination;
                    } catch (error) {
                        console.error('Erro ao carregar histórico:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                goToPage(page) {
                    if (page < 1 || page > this.pagination.last_page) return;
                    this.loadHistory(page);
                }
            }
        }

        function productManager() {
            return {
                products: [],
                loading: false,
                successMessage: '',
                filters: {
                    search: '',
                    category_id: '',
                    status: ''
                },
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0
                },
                init() {
                    this.fetchProducts();

                    window.addEventListener('product-saved', (event) => {
                        this.successMessage = event.detail.message;
                        this.fetchProducts();
                        setTimeout(() => this.successMessage = '', 5000);
                    });
                },
                async fetchProducts(page = 1) {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('products.filter') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ...this.filters,
                                page: page,
                                per_page: this.pagination.per_page
                            })
                        });
                        const data = await response.json();
                        this.products = data.products;
                        this.pagination = data.pagination;
                    } catch (error) {
                        console.error('Erro ao buscar produtos:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                goToPage(page) {
                    if (page < 1 || page > this.pagination.last_page) return;
                    this.fetchProducts(page);
                },
                clearFilters() {
                    this.filters.search = '';
                    this.filters.category_id = '';
                    this.filters.status = '';
                    this.fetchProducts(1);
                },
                async toggleStatus(product) {
                    try {
                        const response = await fetch('/products/' + product.uuid + '/toggle-status', {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        product.is_active = data.is_active;
                        this.successMessage = data.message;
                        setTimeout(() => this.successMessage = '', 5000);
                    } catch (error) {
                        console.error('Erro ao alterar status:', error);
                    }
                }
            }
        }
    </script>
</x-app-layout>