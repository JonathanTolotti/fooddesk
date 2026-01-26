<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cardápio - Mesa {{ $table->number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        * { font-family: 'Inter', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-200 min-h-screen" x-data="customerMenu()" x-init="init()">
    <!-- Mobile Container -->
    <div class="max-w-md mx-auto bg-gray-50 min-h-screen relative shadow-2xl">
    <!-- Compact Header -->
    <header class="bg-white sticky top-0 z-40 border-b border-gray-100 max-w-md mx-auto">
        <div class="flex items-center justify-between px-4 py-2">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                    {{ $table->number }}
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-semibold text-gray-900">
                        @if($customer)
                            {{ Str::words($customer->name, 2, '') }}
                        @else
                            Mesa {{ $table->number }}
                        @endif
                    </p>
                </div>
            </div>
            <button @click="callWaiter()"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-full transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Garçom
            </button>
        </div>

        <!-- Categories Pills -->
        <div class="flex overflow-x-auto px-4 py-2 gap-2 hide-scrollbar">
            @foreach($categories as $category)
                @if($category->products->count() > 0)
                    <a href="#cat-{{ $category->id }}"
                       class="flex-shrink-0 px-3 py-1 text-xs font-medium rounded-full transition-all"
                       :class="activeCategory === {{ $category->id }}
                           ? 'bg-blue-600 text-white shadow-sm'
                           : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                       @click="activeCategory = {{ $category->id }}">
                        {{ $category->name }}
                    </a>
                @endif
            @endforeach
        </div>
    </header>

    <!-- Toast -->
    <div x-show="toast.show" x-transition x-cloak
         class="fixed top-16 left-1/2 -translate-x-1/2 w-full max-w-md px-4 z-50">
        <div :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
             class="text-white text-sm px-4 py-2.5 rounded-xl shadow-lg flex items-center justify-between">
            <span x-text="toast.message"></span>
            <button @click="toast.show = false" class="ml-2 opacity-70 hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Products -->
    <main class="pb-28 px-4 max-w-md mx-auto">
        @foreach($categories as $category)
            @if($category->products->count() > 0)
                <section id="cat-{{ $category->id }}" class="pt-4">
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wide mb-2">{{ $category->name }}</h2>
                    <div class="space-y-2">
                        @foreach($category->products as $product)
                            <div class="bg-white rounded-xl p-3 shadow-sm active:scale-[0.98] transition-transform cursor-pointer"
                                 @click="openProduct({{ $product->id }})">
                                <div class="flex gap-3">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex-shrink-0 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-sm">{{ $product->name }}</h3>
                                        @if($product->description)
                                            <p class="text-xs text-gray-500 line-clamp-2 mt-0.5">{{ $product->description }}</p>
                                        @endif
                                        <p class="text-blue-600 font-bold text-sm mt-1">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-7 h-7 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    </div><!-- End Mobile Container -->

    <!-- Bottom Cart Bar -->
    <div x-show="order && order.items_count > 0" x-cloak
         class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] z-30">
        <div class="px-4 py-3">
            <div class="flex items-center gap-3">
                <button @click="showCart = true"
                        class="flex-1 flex items-center justify-between bg-gray-50 hover:bg-gray-100 rounded-xl px-4 py-2.5 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full flex items-center justify-center" x-text="order.items_count"></span>
                        <span class="text-sm font-medium text-gray-700">Ver pedido</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">R$ <span x-text="formatPrice(order.total)"></span></span>
                </button>
                <button @click="sendToKitchen()"
                        x-show="order.pending_count > 0"
                        :disabled="sending"
                        class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 disabled:from-gray-300 disabled:to-gray-400 text-white text-sm font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2">
                    <svg x-show="!sending" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg x-show="sending" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="sending ? 'Enviando...' : 'Pedir'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div x-show="showProductModal" x-cloak class="fixed inset-0 z-50" @keydown.escape.window="showProductModal = false">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showProductModal = false"></div>
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white rounded-t-3xl max-h-[85vh] overflow-hidden"
             x-show="showProductModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-y-full"
             x-transition:enter-end="transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-y-0"
             x-transition:leave-end="transform translate-y-full">
            <template x-if="selectedProduct">
                <div class="flex flex-col max-h-[85vh]">
                    <!-- Handle -->
                    <div class="flex justify-center pt-2 pb-1">
                        <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
                    </div>

                    <!-- Scrollable Content -->
                    <div class="flex-1 overflow-y-auto hide-scrollbar px-4 pb-4">
                        <!-- Product Header -->
                        <div class="flex gap-3 items-start">
                            <div x-show="selectedProduct.image" class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0">
                                <img :src="selectedProduct.image" :alt="selectedProduct.name" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0 pt-1">
                                <h3 class="text-lg font-bold text-gray-900 leading-tight" x-text="selectedProduct.name"></h3>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2" x-text="selectedProduct.description"></p>
                                <p class="text-lg font-bold text-blue-600 mt-1">R$ <span x-text="formatPrice(selectedProduct.price)"></span></p>
                            </div>
                            <button @click="showProductModal = false" class="p-1.5 -mt-1 -mr-1 rounded-full hover:bg-gray-100">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Remove Ingredients -->
                        <div x-show="selectedProduct.standard_ingredients?.length > 0" class="mt-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Remover</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="ing in selectedProduct.standard_ingredients" :key="ing.id">
                                    <label class="cursor-pointer">
                                        <input type="checkbox" :value="ing.id" x-model="itemForm.removed_ingredients" class="peer hidden">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium border transition-all
                                                     peer-checked:bg-red-50 peer-checked:border-red-200 peer-checked:text-red-700
                                                     bg-white border-gray-200 text-gray-600 hover:border-gray-300">
                                            <span class="peer-checked:line-through" x-text="'Sem ' + ing.name"></span>
                                        </span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Add Ingredients -->
                        <div x-show="selectedProduct.additional_ingredients?.length > 0" class="mt-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Adicionais</p>
                            <div class="space-y-1.5">
                                <template x-for="ing in selectedProduct.additional_ingredients" :key="ing.id">
                                    <label class="flex items-center justify-between p-2.5 rounded-xl cursor-pointer transition-all"
                                           :class="itemForm.added_ingredients.includes(String(ing.id)) ? 'bg-green-50 ring-1 ring-green-200' : 'bg-gray-50 hover:bg-gray-100'">
                                        <div class="flex items-center gap-2.5">
                                            <input type="checkbox"
                                                   :value="String(ing.id)"
                                                   x-model="itemForm.added_ingredients"
                                                   class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                            <span class="text-sm text-gray-700" x-text="ing.name"></span>
                                        </div>
                                        <span class="text-xs font-semibold text-green-600" x-text="'+ R$ ' + formatPrice(ing.price)"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mt-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Observações</p>
                            <textarea x-model="itemForm.notes"
                                      placeholder="Ex: Bem passado, sem cebola..."
                                      rows="2"
                                      class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none bg-gray-50"></textarea>
                        </div>
                    </div>

                    <!-- Fixed Bottom -->
                    <div class="border-t border-gray-100 bg-white px-4 py-3">
                        <!-- Quantity -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-700">Quantidade</span>
                            <div class="flex items-center gap-3 bg-gray-100 rounded-full px-1 py-1">
                                <button @click="itemForm.quantity = Math.max(1, itemForm.quantity - 1)"
                                        class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="w-8 text-center text-base font-bold text-gray-900" x-text="itemForm.quantity"></span>
                                <button @click="itemForm.quantity = Math.min(99, itemForm.quantity + 1)"
                                        class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Add Button -->
                        <button @click="addToOrder()"
                                :disabled="adding"
                                class="w-full py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                            <svg x-show="!adding" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <svg x-show="adding" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="adding ? 'Adicionando...' : 'Adicionar  •  R$ ' + formatPrice(calculateItemTotal())"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Cart Modal -->
    <div x-show="showCart" x-cloak class="fixed inset-0 z-50" @keydown.escape.window="showCart = false">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showCart = false"></div>
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white rounded-t-3xl max-h-[80vh] overflow-hidden flex flex-col"
             x-show="showCart"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-y-full"
             x-transition:enter-end="transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-y-0"
             x-transition:leave-end="transform translate-y-full">
            <!-- Handle & Header -->
            <div class="px-4 pt-2 pb-3 border-b border-gray-100">
                <div class="flex justify-center mb-2">
                    <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
                </div>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Seu Pedido</h3>
                    <button @click="showCart = false" class="p-1.5 rounded-full hover:bg-gray-100">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Items -->
            <div class="flex-1 overflow-y-auto hide-scrollbar p-4">
                <template x-if="order?.items?.length > 0">
                    <div class="space-y-2">
                        <template x-for="item in order.items" :key="item.id">
                            <div class="bg-gray-50 rounded-xl p-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-semibold text-sm text-gray-900" x-text="item.quantity + 'x ' + item.product_name"></span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded-full font-medium"
                                                  :class="{
                                                      'bg-yellow-100 text-yellow-700': item.status === 'pending',
                                                      'bg-blue-100 text-blue-700': item.status === 'preparing',
                                                      'bg-green-100 text-green-700': item.status === 'ready',
                                                      'bg-gray-200 text-gray-600': item.status === 'delivered'
                                                  }"
                                                  x-text="item.status_label"></span>
                                        </div>
                                        <template x-if="item.customizations?.length > 0">
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                <template x-for="c in item.customizations" :key="c.name">
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded"
                                                          :class="c.action === 'removed' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'"
                                                          x-text="c.action === 'removed' ? 'Sem ' + c.name : '+ ' + c.name"></span>
                                                </template>
                                            </div>
                                        </template>
                                        <p x-show="item.notes" class="text-[10px] text-gray-400 mt-1" x-text="item.notes"></p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="font-semibold text-sm text-gray-900">R$ <span x-text="formatPrice(item.total_price)"></span></p>
                                        <button x-show="item.can_remove"
                                                @click="removeItem(item.id)"
                                                class="text-[10px] text-red-500 hover:text-red-600 font-medium mt-0.5">
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
            <div class="border-t border-gray-100 bg-gray-50 px-4 py-3">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm text-gray-500">Total</span>
                    <span class="text-xl font-bold text-gray-900">R$ <span x-text="formatPrice(order?.total || 0)"></span></span>
                </div>
                <button @click="sendToKitchen(); showCart = false"
                        x-show="order?.pending_count > 0"
                        :disabled="sending"
                        class="w-full py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                    <svg x-show="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg x-show="sending" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="sending ? 'Enviando...' : 'Realizar Pedido (' + order?.pending_count + ')'"></span>
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
                    setInterval(() => this.refreshOrder(), 30000);
                },

                formatPrice(value) {
                    return parseFloat(value || 0).toFixed(2).replace('.', ',');
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                openProduct(productId) {
                    const product = products[productId];
                    if (!product) return;

                    const ingredients = product.ingredients || [];

                    this.selectedProduct = {
                        id: product.id,
                        name: product.name,
                        description: product.description,
                        price: parseFloat(product.price),
                        image: product.image ? '/storage/' + product.image : null,
                        standard_ingredients: ingredients.filter(i => i.pivot.type === 'standard'),
                        additional_ingredients: ingredients.filter(i => i.pivot.type === 'additional').map(i => ({
                            id: i.id,
                            name: i.name,
                            price: parseFloat(i.pivot.additional_price) || 0
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

                    this.itemForm.added_ingredients.forEach(idStr => {
                        const ing = this.selectedProduct.additional_ingredients.find(i => String(i.id) === String(idStr));
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
                                removed_ingredients: this.itemForm.removed_ingredients.map(Number),
                                added_ingredients: this.itemForm.added_ingredients.map(Number),
                            }),
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.order = data.order;
                            this.showProductModal = false;
                            this.showToast('Item adicionado!');
                        } else {
                            this.showToast(data.message || 'Erro ao adicionar', 'error');
                        }
                    } catch (error) {
                        this.showToast('Erro de conexão', 'error');
                    } finally {
                        this.adding = false;
                    }
                },

                async removeItem(itemId) {
                    if (!confirm('Remover este item?')) return;

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
                            this.showToast('Item removido');
                        } else {
                            this.showToast(data.message || 'Erro', 'error');
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
                            this.showToast('Pedido realizado!');
                        } else {
                            this.showToast(data.message || 'Erro', 'error');
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
                        console.error('Erro:', error);
                    }
                },
            };
        }
    </script>
</body>
</html>
