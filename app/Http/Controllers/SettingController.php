<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService
    ) {}

    public function index(): View
    {
        $settings = $this->settingService->all();
        $groupedSettings = $settings->groupBy('category');

        $categories = [
            'establishment' => 'Estabelecimento',
            'service' => 'Serviço',
            'kitchen' => 'Cozinha',
            'table' => 'Mesas',
            'hours' => 'Horários',
        ];

        return view('settings.index', compact('groupedSettings', 'categories'));
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $settingsData = $request->validated()['settings'];

        // Process checkbox/toggle values
        $booleanFields = ['service_fee_enabled', 'enable_qr_self_service'];
        foreach ($booleanFields as $field) {
            $settingsData[$field] = isset($settingsData[$field]) && $settingsData[$field] ? '1' : '0';
        }

        // Process operating_days to JSON
        if (isset($settingsData['operating_days'])) {
            $settingsData['operating_days'] = array_map('intval', $settingsData['operating_days']);
        }

        $this->settingService->updateMultiple($settingsData);

        return response()->json([
            'message' => 'Configurações salvas com sucesso.',
        ]);
    }

    public function history(Request $request, Setting $setting): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $histories = $setting->histories()->with('user')->paginate($perPage);

        return response()->json([
            'setting' => [
                'uuid' => $setting->uuid,
                'key' => $setting->key,
                'label' => $setting->label,
            ],
            'histories' => $histories->getCollection()->map(fn ($history) => [
                'id' => $history->id,
                'event' => $history->event,
                'event_label' => $history->event_label,
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
