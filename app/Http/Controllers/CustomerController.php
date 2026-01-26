<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    /**
     * Search customer by phone
     */
    public function search(SearchCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->searchByPhone($request->validated('phone'));

        if ($customer) {
            return response()->json([
                'found' => true,
                'customer' => $customer,
            ]);
        }

        return response()->json(['found' => false]);
    }

    /**
     * Store new customer
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $result = $this->customerService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => $result['is_new'] ? 'Cliente cadastrado com sucesso.' : 'Cliente já cadastrado.',
            'customer' => $result['customer'],
        ]);
    }
}
