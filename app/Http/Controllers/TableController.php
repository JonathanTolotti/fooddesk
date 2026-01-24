<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Models\Table;
use App\Services\TableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    public function __construct(
        private readonly TableService $tableService
    ) {}

    public function index(): View
    {
        return view('tables.index');
    }

    public function filter(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status']);
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        request()->merge(['page' => $page]);

        $tables = $this->tableService->filter($filters, $perPage);

        return response()->json([
            'tables' => $tables->getCollection()->map(fn (Table $table) => [
                'id' => $table->id,
                'uuid' => $table->uuid,
                'number' => $table->number,
                'name' => $table->name,
                'capacity' => $table->capacity,
                'status' => $table->status,
                'status_label' => $table->status_label,
                'status_color' => $table->status_color,
                'is_active' => $table->is_active,
            ]),
            'pagination' => [
                'current_page' => $tables->currentPage(),
                'last_page' => $tables->lastPage(),
                'per_page' => $tables->perPage(),
                'total' => $tables->total(),
            ],
        ]);
    }

    public function store(StoreTableRequest $request): JsonResponse
    {
        $table = $this->tableService->create($request->validated());

        return response()->json([
            'message' => 'Mesa criada com sucesso.',
            'table' => [
                'id' => $table->id,
                'uuid' => $table->uuid,
                'number' => $table->number,
                'name' => $table->name,
                'capacity' => $table->capacity,
                'status' => $table->status,
                'status_label' => $table->status_label,
                'status_color' => $table->status_color,
                'is_active' => $table->is_active,
            ],
        ], 201);
    }

    public function update(UpdateTableRequest $request, Table $table): JsonResponse
    {
        $table = $this->tableService->update($table, $request->validated());

        return response()->json([
            'message' => 'Mesa atualizada com sucesso.',
            'table' => [
                'id' => $table->id,
                'uuid' => $table->uuid,
                'number' => $table->number,
                'name' => $table->name,
                'capacity' => $table->capacity,
                'status' => $table->status,
                'status_label' => $table->status_label,
                'status_color' => $table->status_color,
                'is_active' => $table->is_active,
            ],
        ]);
    }

    public function destroy(Table $table): JsonResponse
    {
        $this->tableService->delete($table);

        return response()->json([
            'message' => 'Mesa excluída com sucesso.',
        ]);
    }

    public function toggleStatus(Table $table): JsonResponse
    {
        $table = $this->tableService->toggleStatus($table);

        $status = $table->is_active ? 'ativada' : 'inativada';

        return response()->json([
            'message' => "Mesa {$status} com sucesso.",
            'is_active' => $table->is_active,
        ]);
    }

    public function changeStatus(Request $request, Table $table): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:available,occupied,reserved,cleaning'],
        ]);

        $table = $this->tableService->changeTableStatus($table, $request->input('status'));

        return response()->json([
            'message' => 'Status da mesa alterado com sucesso.',
            'status' => $table->status,
            'status_label' => $table->status_label,
            'status_color' => $table->status_color,
        ]);
    }

    public function history(Request $request, Table $table): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $histories = $table->histories()->with('user')->paginate($perPage);

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
