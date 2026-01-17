<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Não autenticado.');
        }

        // Converte strings para enums e verifica
        $allowedRoles = array_map(
            fn (string $role) => UserRole::from($role),
            $roles
        );

        if (!$user->hasRole(...$allowedRoles)) {
            abort(403, 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);

    }
}
