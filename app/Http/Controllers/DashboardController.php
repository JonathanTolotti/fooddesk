<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    /**
     * Display the dashboard view
     */
    public function index(): View
    {
        $data = $this->dashboardService->getAllData();

        return view('dashboard', $data);
    }

    /**
     * Get dashboard data for AJAX refresh
     */
    public function data(): JsonResponse
    {
        return response()->json($this->dashboardService->getAllData());
    }
}
