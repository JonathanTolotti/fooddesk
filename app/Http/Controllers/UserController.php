<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function filter(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'role', 'status']);
        $page = (int) $request->input('page', 1);
        $paginator = $this->userService->filter($filters, 10, $page);

        return response()->json([
            'users' => $paginator->map(fn ($user) => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'login' => $user->login,
                'role' => $user->role->value,
                'role_label' => $user->role->label(),
                'status' => $user->status,
                'created_at' => $user->created_at->format('d/m/Y'),
            ]),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
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

    public function history(Request $request, User $user): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $histories = $user->histories()->with('user')->paginate($perPage);

        return response()->json([
            'histories' => $histories->getCollection()->map(fn ($history) => [
                'id' => $history->id,
                'event' => $history->event,
                'field' => $history->field,
                'field_label' => $history->field_label,
                'old_value' => $history->formatted_old_value,
                'new_value' => $history->formatted_new_value,
                'user_name' => $history->user?->name ?? 'Sistema',
                'created_at' => $history->created_at->format('d/m/Y H:i'),
            ]),
            'pagination' => [
                'current_page' => $histories->currentPage(),
                'last_page' => $histories->lastPage(),
                'per_page' => $histories->perPage(),
                'total' => $histories->total(),
                'has_more' => $histories->hasMorePages(),
            ],
        ]);
    }
}
