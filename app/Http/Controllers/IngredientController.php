<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Models\Ingredient;
use App\Services\IngredientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function __construct(
        private readonly IngredientService $ingredientService
    ) {}

    public function index(): View
    {
        return view('ingredients.index');
    }

    public function filter(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status']);
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        request()->merge(['page' => $page]);

        $ingredients = $this->ingredientService->filter($filters, $perPage);

        return response()->json([
            'ingredients' => $ingredients->getCollection()->map(fn (Ingredient $ingredient) => [
                'id' => $ingredient->id,
                'uuid' => $ingredient->uuid,
                'name' => $ingredient->name,
                'description' => $ingredient->description,
                'is_active' => $ingredient->is_active,
            ]),
            'pagination' => [
                'current_page' => $ingredients->currentPage(),
                'last_page' => $ingredients->lastPage(),
                'per_page' => $ingredients->perPage(),
                'total' => $ingredients->total(),
            ],
        ]);
    }

    public function store(StoreIngredientRequest $request): JsonResponse
    {
        $ingredient = $this->ingredientService->create($request->validated());

        return response()->json([
            'message' => 'Ingrediente criado com sucesso.',
            'ingredient' => [
                'id' => $ingredient->id,
                'uuid' => $ingredient->uuid,
                'name' => $ingredient->name,
                'description' => $ingredient->description,
                'is_active' => $ingredient->is_active,
            ],
        ], 201);
    }

    public function update(UpdateIngredientRequest $request, Ingredient $ingredient): JsonResponse
    {
        $ingredient = $this->ingredientService->update($ingredient, $request->validated());

        return response()->json([
            'message' => 'Ingrediente atualizado com sucesso.',
            'ingredient' => [
                'id' => $ingredient->id,
                'uuid' => $ingredient->uuid,
                'name' => $ingredient->name,
                'description' => $ingredient->description,
                'is_active' => $ingredient->is_active,
            ],
        ]);
    }

    public function destroy(Ingredient $ingredient): JsonResponse
    {
        $this->ingredientService->delete($ingredient);

        return response()->json([
            'message' => 'Ingrediente excluído com sucesso.',
        ]);
    }

    public function toggleStatus(Ingredient $ingredient): JsonResponse
    {
        $ingredient = $this->ingredientService->toggleStatus($ingredient);

        $status = $ingredient->is_active ? 'ativado' : 'inativado';

        return response()->json([
            'message' => "Ingrediente {$status} com sucesso.",
            'is_active' => $ingredient->is_active,
        ]);
    }

    public function history(Request $request, Ingredient $ingredient): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $histories = $ingredient->histories()->with('user')->paginate($perPage);

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
