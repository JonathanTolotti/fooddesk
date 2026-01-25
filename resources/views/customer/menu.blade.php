<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cardápio - Mesa {{ $table->number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen" x-data="customerMenu()" x-init="init()">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                        {{ $table->number }}
                    </span>
                    <div>
                        <h1 class="font-semibold text-gray-900">
                            @if($customer)
                                Olá, {{ $customer->name }}!
                            @else
                                Mesa {{ $table->number }}
                            @endif
                        </h1>
                        <p class="text-xs text-gray-500">Mesa {{ $table->number }} &bull; Faça seu pedido</p>
                    </div>
                </div>
                <button @click="callWaiter()"
                        class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Chamar Garçom
                </button>
            </div>
        </div>

        <!-- Categories Navigation -->
        <div class="border-t border-gray-100">
            <div class="flex overflow-x-auto px-4 py-2 gap-2 custom-scrollbar">
                @foreach($categories as $category)
                    @if($category->products->count() > 0)
                        <a href="#category-{{ $category->id }}"
                           class="flex-shrink-0 px-4 py-2 text-sm font-medium rounded-full transition-colors"
                           :class="activeCategory === {{ $category->id }} ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                           @click="activeCategory = {{ $category->id }}">
                            {{ $category->name }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </header>

    <!-- Success/Error Toast -->
    <div x-show="toast.show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         class="fixed top-20 left-4 right-4 z-50">
        <div :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
             class="text-white px-4 py-3 rounded-lg shadow-lg flex items-center justify-between">
            <span x-text="toast.message"></span>
            <button @click="toast.show = false" class="ml-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="pb-32">
        @foreach($categories as $category)
            @if($category->products->count() > 0)
                <section id="category-{{ $category->id }}" class="py-4">
                    <h2 class="px-4 text-lg font-bold text-gray-900 mb-3">{{ $category->name }}</h2>
                    <div class="space-y-3 px-4">
                        @foreach($category->products as $product)
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden"
                                 @click="openProductModal({{ $product->id }})">
                                <div class="flex">
                                    @if($product->image)
                                        <div class="w-24 h-24 flex-shrink-0">
                                            <img src="{{ Storage::url($product->image) }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                    <div class="flex-1 p-3 {{ $product->image ? '' : 'pl-4' }}">
                                        <h3 class="font-medium text-gray-900">{{ $product->name }}</h3>
                                        @if($product->description)
                                            <p class="text-sm text-gray-500 line-clamp-2 mt-1">{{ $product->description }}</p>
                                        @endif
                                        <p class="text-blue-600 font-bold mt-2">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center pr-3">
                                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach
    </main>

    <!-- Cart Summary (Fixed Bottom) -->
    <div x-show="order && order.items_count > 0"
         x-cloak
         class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-30">
        <div class="px-4 py-3">
            <!-- Order Summary -->
            <div class="flex items-center justify-between mb-3">
                <div>
                    <span class="text-sm text-gray-500">Seu pedido</span>
                    <p class="font-bold text-gray-900">
                        <span x-text="order.items_count"></span> item(ns) &bull;
                        R$ <span x-text="formatPrice(order.total)"></span>
                    </p>
                </div>
                <button @click="showCart = true"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                    Ver pedido
                </button>
            </div>

            <!-- Send to Kitchen Button -->
            <button @click="sendToKitchen()"
                    x-show="order.pending_count > 0"
                    :disabled="sending"
                    class="w-full py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                <svg x-show="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <svg x-show="sending" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="sending ? 'Enviando...' : 'Enviar para Cozinha (' + order.pending_count + ' pendente' + (order.pending_count > 1 ? 's' : '') + ')'"></span>
            </button>
        </div>
    </div>

    <!-- Product Modal -->
    <div x-show="showProductModal"
         x-cloak
         class="fixed inset-0 z-50"
         @keydown.escape.window="showProductModal = false">
        <div class="absolute inset-0 bg-black/50" @click="showProductModal = false"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl max-h-[90vh] overflow-y-auto custom-scrollbar"
             x-show="showProductModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-y-full"
             x-transition:enter-end="transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-y-0"
             x-transition:leave-end="transform translate-y-full">
            <template x-if="selectedProduct">
                <div>
                    <!-- Product Image -->
                    <div x-show="selectedProduct.image" class="h-48 bg-gray-100">
                        <img :src="selectedProduct.image" :alt="selectedProduct.name" class="w-full h-full object-cover">
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900" x-text="selectedProduct.name"></h3>
                                <p class="text-gray-500 mt-1" x-text="selectedProduct.description"></p>
                            </div>
                            <button @click="showProductModal = false" class="p-2 -mt-2 -mr-2">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <p class="text-2xl font-bold text-blue-600 mt-3">
                            R$ <span x-text="formatPrice(selectedProduct.price)"></span>
                        </p>

                        <!-- Standard Ingredients (can be removed) -->
                        <div x-show="selectedProduct.standard_ingredients && selectedProduct.standard_ingredients.length > 0" class="mt-4">
                            <h4 class="font-medium text-gray-900 mb-2">Remover ingredientes:</h4>
                            <div class="space-y-2">
                                <template x-for="ingredient in selectedProduct.standard_ingredients" :key="ingredient.id">
                                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer">
                                        <input type="checkbox"
                                               :value="ingredient.id"
                                               x-model="itemForm.removed_ingredients"
                                               class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        <span class="text-gray-700" x-text="'Sem ' + ingredient.name"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Additional Ingredients -->
                        <div x-show="selectedProduct.additional_ingredients && selectedProduct.additional_ingredients.length > 0" class="mt-4">
                            <h4 class="font-medium text-gray-900 mb-2">Adicionar:</h4>
                            <div class="space-y-2">
                                <template x-for="ingredient in selectedProduct.additional_ingredients" :key="ingredient.id">
                                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox"
                                                   :value="ingredient.id"
                                                   x-model="itemForm.added_ingredients"
                                                   class="w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                            <span class="text-gray-700" x-text="ingredient.name"></span>
                                        </div>
                                        <span class="text-green-600 font-medium" x-text="'+ R$ ' + formatPrice(ingredient.price)"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-900 mb-2">Observações:</h4>
                            <textarea x-model="itemForm.notes"
                                      placeholder="Ex: Bem passado, sem cebola..."
                                      rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                        </div>

                        <!-- Quantity -->
                        <div class="mt-4 flex items-center justify-between">
                            <span class="font-medium text-gray-900">Quantidade:</span>
                            <div class="flex items-center gap-3">
                                <button @click="itemForm.quantity = Math.max(1, itemForm.quantity - 1)"
                                        class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="w-8 text-center text-xl font-bold" x-text="itemForm.quantity"></span>
                                <button @click="itemForm.quantity = Math.min(99, itemForm.quantity + 1)"
                                        class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Add Button -->
                        <button @click="addToOrder()"
                                :disabled="adding"
                                class="w-full mt-6 py-4 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                            <svg x-show="!adding" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <svg x-show="adding" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="adding ? 'Adicionando...' : 'Adicionar R$ ' + formatPrice(calculateItemTotal())"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Cart Modal -->
    <div x-show="showCart"
         x-cloak
         class="fixed inset-0 z-50"
         @keydown.escape.window="showCart = false">
        <div class="absolute inset-0 bg-black/50" @click="showCart = false"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl max-h-[85vh] overflow-hidden flex flex-col"
             x-show="showCart"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-y-full"
             x-transition:enter-end="transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-y-0"
             x-transition:leave-end="transform translate-y-full">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Seu Pedido</h3>
                <button @click="showCart = false" class="p-2 -mr-2">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Items List -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4">
                <template x-if="order && order.items && order.items.length > 0">
                    <div class="space-y-3">
                        <template x-for="item in order.items" :key="item.id">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-900" x-text="item.quantity + 'x ' + item.product_name"></span>
                                            <span class="text-xs px-2 py-0.5 rounded-full"
                                                  :class="{
                                                      'bg-yellow-100 text-yellow-800': item.status === 'pending',
                                                      'bg-blue-100 text-blue-800': item.status === 'preparing',
                                                      'bg-green-100 text-green-800': item.status === 'ready',
                                                      'bg-gray-100 text-gray-800': item.status === 'delivered'
                                                  }"
                                                  x-text="item.status_label"></span>
                                        </div>
                                        <!-- Customizations -->
                                        <template x-if="item.customizations && item.customizations.length > 0">
                                            <div class="mt-1 space-y-0.5">
                                                <template x-for="custom in item.customizations" :key="custom.name">
                                                    <p class="text-xs"
                                                       :class="custom.action === 'removed' ? 'text-red-600' : 'text-green-600'"
                                                       x-text="custom.action === 'removed' ? 'Sem ' + custom.name : 'Com ' + custom.name + (custom.price > 0 ? ' (+R$ ' + formatPrice(custom.price) + ')' : '')"></p>
                                                </template>
                                            </div>
                                        </template>
                                        <p x-show="item.notes" class="text-xs text-gray-500 mt-1" x-text="'Obs: ' + item.notes"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">R$ <span x-text="formatPrice(item.total_price)"></span></p>
                                        <button x-show="item.can_remove"
                                                @click="removeItem(item.id)"
                                                class="text-xs text-red-600 hover:text-red-700 mt-1">
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900">R$ <span x-text="formatPrice(order?.subtotal || 0)"></span></span>
                    </div>
                    <div x-show="order?.service_fee > 0" class="flex justify-between text-sm">
                        <span class="text-gray-600">Taxa de serviço (10%)</span>
                        <span class="text-gray-900">R$ <span x-text="formatPrice(order?.service_fee || 0)"></span></span>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span class="text-blue-600">R$ <span x-text="formatPrice(order?.total || 0)"></span></span>
                    </div>
                </div>

                <button @click="sendToKitchen(); showCart = false"
                        x-show="order?.pending_count > 0"
                        :disabled="sending"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold rounded-xl transition-colors">
                    <span x-text="sending ? 'Enviando...' : 'Enviar para Cozinha'"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
        const products = @json($categories->flatMap->products->keyBy('id'));

        function customerMenu() {
            return {
                tableUuid: '{{ $table->uuid }}',
                order: @json($orderData),
                activeCategory: {{ $categories->first()?->id ?? 'null' }},
                showProductModal: false,
                showCart: false,
                selectedProduct: null,
                itemForm: {
                    quantity: 1,
                    notes: '',
                    removed_ingredients: [],
                    added_ingredients: [],
                },
                adding: false,
                sending: false,
                toast: { show: false, message: '', type: 'success' },

                init() {
                    // Refresh order every 30 seconds
                    setInterval(() => this.refreshOrder(), 30000);
                },

                formatPrice(value) {
                    return parseFloat(value || 0).toFixed(2).replace('.', ',');
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 4000);
                },

                openProductModal(productId) {
                    const product = products[productId];
                    if (!product) return;

                    const ingredients = product.ingredients || [];

                    this.selectedProduct = {
                        id: product.id,
                        name: product.name,
                        description: product.description,
                        price: product.price,
                        image: product.image ? '/storage/' + product.image : null,
                        standard_ingredients: ingredients.filter(i => i.pivot.type === 'standard'),
                        additional_ingredients: ingredients.filter(i => i.pivot.type === 'additional').map(i => ({
                            ...i,
                            price: i.pivot.additional_price || 0
                        })),
                    };

                    this.itemForm = {
                        quantity: 1,
                        notes: '',
                        removed_ingredients: [],
                        added_ingredients: [],
                    };

                    this.showProductModal = true;
                },

                calculateItemTotal() {
                    if (!this.selectedProduct) return 0;

                    let total = this.selectedProduct.price;

                    // Add additional ingredients price
                    this.itemForm.added_ingredients.forEach(id => {
                        const ing = this.selectedProduct.additional_ingredients.find(i => i.id == id);
                        if (ing) total += ing.price;
                    });

                    return total * this.itemForm.quantity;
                },

                async addToOrder() {
                    if (this.adding) return;
                    this.adding = true;

                    try {
                        const response = await fetch(`/menu/${this.tableUuid}/items`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                product_id: this.selectedProduct.id,
                                quantity: this.itemForm.quantity,
                                notes: this.itemForm.notes,
                                removed_ingredients: this.itemForm.removed_ingredients,
                                added_ingredients: this.itemForm.added_ingredients,
                            }),
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.order = data.order;
                            this.showProductModal = false;
                            this.showToast(data.message);
                        } else {
                            this.showToast(data.message || 'Erro ao adicionar item', 'error');
                        }
                    } catch (error) {
                        this.showToast('Erro de conexão', 'error');
                    } finally {
                        this.adding = false;
                    }
                },

                async removeItem(itemId) {
                    if (!confirm('Remover este item do pedido?')) return;

                    try {
                        const response = await fetch(`/menu/${this.tableUuid}/items/${itemId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.order = data.order;
                            this.showToast(data.message);
                        } else {
                            this.showToast(data.message || 'Erro ao remover item', 'error');
                        }
                    } catch (error) {
                        this.showToast('Erro de conexão', 'error');
                    }
                },

                async sendToKitchen() {
                    if (this.sending || !this.order?.pending_count) return;
                    this.sending = true;

                    try {
                        const response = await fetch(`/menu/${this.tableUuid}/send-to-kitchen`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.order = data.order;
                            this.showToast(data.message);
                        } else {
                            this.showToast(data.message || 'Erro ao enviar pedido', 'error');
                        }
                    } catch (error) {
                        this.showToast('Erro de conexão', 'error');
                    } finally {
                        this.sending = false;
                    }
                },

                async callWaiter() {
                    try {
                        const response = await fetch(`/menu/${this.tableUuid}/call-waiter`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();
                        this.showToast(data.message);
                    } catch (error) {
                        this.showToast('Erro ao chamar garçom', 'error');
                    }
                },

                async refreshOrder() {
                    try {
                        const response = await fetch(`/menu/${this.tableUuid}/order`, {
                            headers: { 'Accept': 'application/json' },
                        });
                        const data = await response.json();
                        if (data.order) {
                            this.order = data.order;
                        }
                    } catch (error) {
                        console.error('Erro ao atualizar pedido:', error);
                    }
                },
            };
        }
    </script>
</body>
</html>
