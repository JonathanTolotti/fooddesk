<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    /**
     * Search customer by phone number
     */
    public function searchByPhone(string $phone): ?array
    {
        $cleanPhone = preg_replace('/\D/', '', $phone);

        if (strlen($cleanPhone) < 10) {
            return null;
        }

        $customer = Customer::where('phone', $cleanPhone)->first();

        if (! $customer) {
            return null;
        }

        return $this->formatCustomer($customer);
    }

    /**
     * Register a new customer or return existing if phone exists
     */
    public function register(array $data): array
    {
        $phone = preg_replace('/\D/', '', $data['phone']);

        // Check if phone already exists
        $existing = Customer::where('phone', $phone)->first();
        if ($existing) {
            return [
                'customer' => $this->formatCustomer($existing),
                'is_new' => false,
            ];
        }

        $customer = Customer::create([
            'name' => $data['name'],
            'phone' => $phone,
            'birth_date' => $data['birth_date'] ?? null,
        ]);

        return [
            'customer' => $this->formatCustomer($customer),
            'is_new' => true,
        ];
    }

    /**
     * Find customer by ID
     */
    public function findById(int $id): ?Customer
    {
        return Customer::find($id);
    }

    /**
     * Format customer data for API response
     */
    public function formatCustomer(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->formatted_phone,
            'birth_date' => $customer->birth_date?->format('d/m/Y'),
            'is_birthday' => $customer->is_birthday,
        ];
    }
}
