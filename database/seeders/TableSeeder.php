<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            // Mesas internas
            ['number' => 1, 'name' => null, 'capacity' => 4, 'status' => 'available'],
            ['number' => 2, 'name' => null, 'capacity' => 4, 'status' => 'available'],
            ['number' => 3, 'name' => null, 'capacity' => 4, 'status' => 'available'],
            ['number' => 4, 'name' => null, 'capacity' => 2, 'status' => 'available'],
            ['number' => 5, 'name' => null, 'capacity' => 2, 'status' => 'available'],
            ['number' => 6, 'name' => null, 'capacity' => 6, 'status' => 'available'],
            ['number' => 7, 'name' => null, 'capacity' => 6, 'status' => 'available'],
            ['number' => 8, 'name' => null, 'capacity' => 8, 'status' => 'available'],

            // Mesas da varanda
            ['number' => 9, 'name' => 'Varanda 1', 'capacity' => 4, 'status' => 'available'],
            ['number' => 10, 'name' => 'Varanda 2', 'capacity' => 4, 'status' => 'available'],
            ['number' => 11, 'name' => 'Varanda 3', 'capacity' => 6, 'status' => 'available'],

            // Mesa VIP
            ['number' => 12, 'name' => 'VIP', 'capacity' => 10, 'status' => 'available'],
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}
