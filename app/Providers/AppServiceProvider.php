<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerGates();
    }

    private function registerGates(): void
    {
        Gate::before(function (User $user, string $ability) {
            if ($user->isManager()) {
                return true;
            }
        });

        // Gestão de produtos e cardápio
        Gate::define('manage-products', function (User $user) {
            return $user->isManager(); // só gerente por enquanto
        });

        // Gestão de pedidos (criar, editar status)
        Gate::define('manage-orders', function (User $user) {
            return $user->hasRole(UserRole::Manager, UserRole::Waiter);
        });

        // Visualizar fila da cozinha
        Gate::define('view-kitchen', function (User $user) {
            return $user->hasRole(UserRole::Manager, UserRole::Kitchen);
        });

        // Gestão de usuários
        Gate::define('manage-users', function (User $user) {
            return $user->isManager();
        });
    }
}
