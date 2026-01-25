<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Search customer by phone
     */
    public function search(Request $request): JsonResponse
    {
        $phone = preg_replace('/\D/', '', $request->input('phone', ''));

        if (strlen($phone) < 10) {
            return response()->json(['found' => false, 'message' => 'Telefone inválido']);
        }

        $customer = Customer::where('phone', $phone)->first();

        if ($customer) {
            return response()->json([
                'found' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->formatted_phone,
                    'birth_date' => $customer->birth_date?->format('d/m/Y'),
                    'is_birthday' => $customer->is_birthday,
                ],
            ]);
        }

        return response()->json(['found' => false]);
    }

    /**
     * Store new customer
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'birth_date' => 'nullable|date',
        ], [
            'name.required' => 'Informe o nome do cliente.',
            'phone.required' => 'Informe o telefone do cliente.',
        ]);

        $phone = preg_replace('/\D/', '', $validated['phone']);

        // Check if phone already exists
        $existing = Customer::where('phone', $phone)->first();
        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Cliente já cadastrado.',
                'customer' => [
                    'id' => $existing->id,
                    'name' => $existing->name,
                    'phone' => $existing->formatted_phone,
                ],
            ]);
        }

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $phone,
            'birth_date' => $validated['birth_date'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->formatted_phone,
            ],
        ]);
    }
}
