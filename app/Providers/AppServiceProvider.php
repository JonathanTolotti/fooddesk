<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );
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
