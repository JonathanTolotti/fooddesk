<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 transition-colors duration-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @can('manage-orders')
                        <x-nav-link :href="route('reception.index')" :active="request()->routeIs('reception.*')">
                            Recepção
                        </x-nav-link>
                        <x-nav-link :href="route('waiter.index')" :active="request()->routeIs('waiter.*')">
                            Garçom
                        </x-nav-link>
                        <x-nav-link :href="route('kitchen.index')" :active="request()->routeIs('kitchen.*')" target="_blank">
                            Cozinha
                        </x-nav-link>
                        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                            Pedidos
                        </x-nav-link>
                    @endcan

                    @can('manage-products')
                        <!-- Menu Cadastros -->
                        <x-nav-dropdown :active="request()->routeIs('categories.*') || request()->routeIs('products.*') || request()->routeIs('ingredients.*') || request()->routeIs('tables.*')">
                            <x-slot name="trigger">
                                Cadastros
                            </x-slot>
                            <x-slot name="content">
                                <x-nav-dropdown-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                                    Categorias
                                </x-nav-dropdown-link>
                                <x-nav-dropdown-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                    Produtos
                                </x-nav-dropdown-link>
                                <x-nav-dropdown-link :href="route('ingredients.index')" :active="request()->routeIs('ingredients.*')">
                                    Ingredientes
                                </x-nav-dropdown-link>
                                <x-nav-dropdown-link :href="route('tables.index')" :active="request()->routeIs('tables.index')">
                                    Mesas
                                </x-nav-dropdown-link>
                                <x-nav-dropdown-link :href="route('tables.qrcodes')" :active="request()->routeIs('tables.qrcodes')">
                                    QR Codes
                                </x-nav-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>
                    @endcan

                    <!-- Menu Sistema -->
                    @can('manage-users')
                        <x-nav-dropdown :active="request()->routeIs('users.*')">
                            <x-slot name="trigger">
                                Sistema
                            </x-slot>
                            <x-slot name="content">
                                <x-nav-dropdown-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                    Usuários
                                </x-nav-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <!-- Dark Mode Toggle -->
                        <button type="button"
                                @click="$store.darkMode.toggle()"
                                class="w-full flex items-center justify-between px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out">
                            <span>Modo Escuro</span>
                            <div class="relative">
                                <div :class="$store.darkMode.on ? 'bg-blue-600' : 'bg-gray-300'"
                                     class="w-10 h-5 rounded-full transition-colors duration-200"></div>
                                <div :class="$store.darkMode.on ? 'translate-x-5' : 'translate-x-0'"
                                     class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"></div>
                            </div>
                        </button>

                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @can('manage-orders')
                <x-responsive-nav-link :href="route('reception.index')" :active="request()->routeIs('reception.*')">
                    Recepção
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('waiter.index')" :active="request()->routeIs('waiter.*')">
                    Garçom
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kitchen.index')" target="_blank">
                    Cozinha
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                    Pedidos
                </x-responsive-nav-link>
            @endcan

            <!-- Menu Cadastros (Mobile) -->
            <div x-data="{ openCadastros: false }">
                <button @click="openCadastros = !openCadastros"
                        class="w-full flex items-center justify-between ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('categories.*') || request()->routeIs('products.*') || request()->routeIs('ingredients.*') || request()->routeIs('tables.*') ? 'border-indigo-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} text-start text-base font-medium transition duration-150 ease-in-out">
                    <span>Cadastros</span>
                    <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': openCadastros }" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="openCadastros" x-transition class="ps-4">
                    <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                        Categorias
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        Produtos
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('ingredients.index')" :active="request()->routeIs('ingredients.*')">
                        Ingredientes
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tables.index')" :active="request()->routeIs('tables.index')">
                        Mesas
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tables.qrcodes')" :active="request()->routeIs('tables.qrcodes')">
                        QR Codes
                    </x-responsive-nav-link>
                </div>
            </div>

            <!-- Menu Sistema (Mobile) -->
            @can('manage-users')
                <div x-data="{ openSistema: false }">
                    <button @click="openSistema = !openSistema"
                            class="w-full flex items-center justify-between ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('users.*') ? 'border-indigo-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} text-start text-base font-medium transition duration-150 ease-in-out">
                        <span>Sistema</span>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': openSistema }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="openSistema" x-transition class="ps-4">
                        <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            Usuários
                        </x-responsive-nav-link>
                    </div>
                </div>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Dark Mode Toggle -->
                <button type="button"
                        @click="$store.darkMode.toggle()"
                        class="w-full flex items-center justify-between ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition duration-150 ease-in-out">
                    <span>Modo Escuro</span>
                    <div class="relative">
                        <div :class="$store.darkMode.on ? 'bg-blue-600' : 'bg-gray-300'"
                             class="w-10 h-5 rounded-full transition-colors duration-200"></div>
                        <div :class="$store.darkMode.on ? 'translate-x-5' : 'translate-x-0'"
                             class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"></div>
                    </div>
                </button>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
