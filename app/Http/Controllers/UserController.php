<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function index(): View
    {
        $users = $this->userService->paginate();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = UserRole::cases();

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return Redirect::route('users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user): View
    {
        $roles = UserRole::cases();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->update($user, $request->validated());

        return Redirect::route('users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->userService->delete($user);

        return Redirect::route('users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $this->userService->toggleStatus($user);

        $status = $user->status ? 'ativado' : 'inativado';

        return Redirect::route('users.index')
            ->with('success', "Usuário {$status} com sucesso.");
    }
}