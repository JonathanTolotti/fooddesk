<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Services\OrderService;
use App\Services\TableService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReceptionController extends Controller
{
    public function __construct(
        private readonly TableService $tableService,
        private readonly OrderService $orderService
    ) {}

    /**
     * Display the reception view with tables grid
     */
    public function index(): View
    {
        return view('reception.index');
    }

    /**
     * Get tables data for the reception view (AJAX)
     */
    public function tables(): JsonResponse
    {
        $tables = $this->tableService->allActive();

        $tablesData = $tables->map(function (Table $table) {
            $currentOrder = $table->currentOrder();

            return [
                'id' => $table->id,
                'uuid' => $table->uuid,
                'number' => $table->number,
                'name' => $table->name,
                'capacity' => $table->capacity,
                'status' => $table->status,
                'status_label' => $table->status_label,
                'status_color' => $table->status_color,
                'has_order' => $currentOrder !== null,
                'order' => $currentOrder ? [
                    'uuid' => $currentOrder->uuid,
                    'total' => (float) $currentOrder->total,
                    'items_count' => $currentOrder->items()->where('status', '!=', 'cancelled')->count(),
                    'opened_at' => $currentOrder->opened_at->format('H:i'),
                    'duration_minutes' => (int) $currentOrder->opened_at->diffInMinutes(now()),
                    'customer_name' => $currentOrder->customer_name,
                ] : null,
            ];
        });

        return response()->json([
            'tables' => $tablesData,
        ]);
    }
}
