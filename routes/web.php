<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerMenuController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TableQrCodeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaiterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public routes - Customer Self-Service
Route::prefix('menu/{tableUuid}')->group(function () {
    Route::get('/', [CustomerMenuController::class, 'index'])->name('customer.menu');
    Route::post('/search-customer', [CustomerMenuController::class, 'searchCustomer'])->name('customer.search');
    Route::post('/register-customer', [CustomerMenuController::class, 'registerCustomer'])->name('customer.register');
    Route::get('/order', [CustomerMenuController::class, 'getOrder'])->name('customer.order');
    Route::post('/items', [CustomerMenuController::class, 'addItem'])->name('customer.items.add');
    Route::delete('/items/{itemId}', [CustomerMenuController::class, 'removeItem'])->name('customer.items.remove');
    Route::post('/send-to-kitchen', [CustomerMenuController::class, 'sendToKitchen'])->name('customer.send-to-kitchen');
    Route::post('/call-waiter', [CustomerMenuController::class, 'callWaiter'])->name('customer.call-waiter');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customers (for reception)
    Route::post('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

    // Recepção (Tables Grid)
    Route::middleware('can:manage-orders')->group(function () {
        Route::get('/reception', [ReceptionController::class, 'index'])->name('reception.index');
        Route::get('/reception/tables', [ReceptionController::class, 'tables'])->name('reception.tables');
    });

    // Garçom (Waiter Dashboard)
    Route::middleware('can:manage-orders')->group(function () {
        Route::get('/waiter', [WaiterController::class, 'index'])->name('waiter.index');
        Route::get('/waiter/orders', [WaiterController::class, 'orders'])->name('waiter.orders');
        Route::post('/waiter/tables/{table}/acknowledge', [WaiterController::class, 'acknowledgeCall'])->name('waiter.acknowledge');
    });

    // Cozinha (Kitchen Display)
    Route::middleware('can:manage-orders')->group(function () {
        Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
        Route::get('/kitchen/items', [KitchenController::class, 'items'])->name('kitchen.items');
        Route::patch('/kitchen/items/{item}/ready', [KitchenController::class, 'markReady'])->name('kitchen.items.ready');
        Route::patch('/kitchen/orders/{orderUuid}/ready', [KitchenController::class, 'markOrderReady'])->name('kitchen.orders.ready');
    });

    // Cadastros (Categories, Products, Ingredients)
    Route::middleware('can:manage-products')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories/filter', [CategoryController::class, 'filter'])->name('categories.filter');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::patch('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
        Route::get('/categories/{category}/history', [CategoryController::class, 'history'])->name('categories.history');

        // Products
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products/filter', [ProductController::class, 'filter'])->name('products.filter');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::post('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::post('/products/reorder', [ProductController::class, 'reorder'])->name('products.reorder');
        Route::get('/products/{product}/history', [ProductController::class, 'history'])->name('products.history');

        // Ingredients
        Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
        Route::post('/ingredients/filter', [IngredientController::class, 'filter'])->name('ingredients.filter');
        Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
        Route::put('/ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
        Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');
        Route::patch('/ingredients/{ingredient}/toggle-status', [IngredientController::class, 'toggleStatus'])->name('ingredients.toggle-status');
        Route::get('/ingredients/{ingredient}/history', [IngredientController::class, 'history'])->name('ingredients.history');

        // Tables
        Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
        Route::post('/tables/filter', [TableController::class, 'filter'])->name('tables.filter');
        Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
        Route::put('/tables/{table}', [TableController::class, 'update'])->name('tables.update');
        Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');
        Route::patch('/tables/{table}/toggle-status', [TableController::class, 'toggleStatus'])->name('tables.toggle-status');
        Route::patch('/tables/{table}/change-status', [TableController::class, 'changeStatus'])->name('tables.change-status');
        Route::get('/tables/{table}/history', [TableController::class, 'history'])->name('tables.history');

        // QR Codes
        Route::get('/tables/qrcodes', [TableQrCodeController::class, 'index'])->name('tables.qrcodes');
        Route::get('/tables/{table}/qrcode', [TableQrCodeController::class, 'show'])->name('tables.qrcode.show');
        Route::get('/tables/{table}/qrcode/download', [TableQrCodeController::class, 'download'])->name('tables.qrcode.download');
    });

    // Pedidos (Orders)
    Route::middleware('can:manage-orders')->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/filter', [OrderController::class, 'filter'])->name('orders.filter');
        Route::get('/orders/open', [OrderController::class, 'openOrders'])->name('orders.open');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/from-table/{table}', [OrderController::class, 'openFromTable'])->name('orders.from-table');

        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/data', [OrderController::class, 'getData'])->name('orders.data');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::get('/orders/{order}/history', [OrderController::class, 'history'])->name('orders.history');
        Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');

        // Order Items
        Route::post('/orders/{order}/items', [OrderController::class, 'addItem'])->name('orders.items.add');
        Route::put('/orders/{order}/items/{item}', [OrderController::class, 'updateItem'])->name('orders.items.update');
        Route::put('/orders/{order}/items/{item}/ingredients', [OrderController::class, 'updateItemIngredients'])->name('orders.items.ingredients');
        Route::delete('/orders/{order}/items/{item}/cancel', [OrderController::class, 'cancelItem'])->name('orders.items.cancel');
        Route::post('/orders/{order}/send-to-kitchen', [OrderController::class, 'sendToKitchen'])->name('orders.send-to-kitchen');
        Route::post('/orders/{order}/deliver-items', [OrderController::class, 'deliverItems'])->name('orders.deliver-items');
        Route::patch('/orders/{order}/items/{item}/ready', [OrderController::class, 'markItemReady'])->name('orders.items.ready');
        Route::patch('/orders/{order}/items/{item}/delivered', [OrderController::class, 'markItemDelivered'])->name('orders.items.delivered');

        // Payments
        Route::post('/orders/{order}/payments', [OrderController::class, 'addPayment'])->name('orders.payments.add');
        Route::delete('/orders/{order}/payments/{payment}', [OrderController::class, 'removePayment'])->name('orders.payments.remove');

        // Order Actions
        Route::post('/orders/{order}/discount', [OrderController::class, 'applyDiscount'])->name('orders.discount');
        Route::post('/orders/{order}/service-fee', [OrderController::class, 'toggleServiceFee'])->name('orders.service-fee');
        Route::post('/orders/{order}/close', [OrderController::class, 'close'])->name('orders.close');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/transfer', [OrderController::class, 'transfer'])->name('orders.transfer');
    });

    // Sistema (Users)
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/filter', [UserController::class, 'filter'])->name('users.filter');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('users/{user}/history', [UserController::class, 'history'])->name('users.history');
    });
});

require __DIR__.'/auth.php';
