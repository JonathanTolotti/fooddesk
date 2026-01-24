<?php

namespace App\Observers;

use App\Models\Table;
use App\Models\TableHistory;
use Illuminate\Support\Facades\Auth;

class TableObserver
{
    protected array $auditableFields = [
        'number',
        'name',
        'capacity',
        'status',
        'is_active',
    ];

    public function created(Table $table): void
    {
        TableHistory::create([
            'table_id' => $table->id,
            'event' => 'created',
            'field' => null,
            'old_value' => null,
            'new_value' => null,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function updated(Table $table): void
    {
        $changes = $table->getChanges();

        foreach ($this->auditableFields as $field) {
            if (array_key_exists($field, $changes)) {
                $oldValue = $table->getOriginal($field);
                $newValue = $changes[$field];

                // Converte boolean para string para armazenamento
                if (is_bool($oldValue)) {
                    $oldValue = $oldValue ? '1' : '0';
                }
                if (is_bool($newValue)) {
                    $newValue = $newValue ? '1' : '0';
                }

                TableHistory::create([
                    'table_id' => $table->id,
                    'event' => 'updated',
                    'field' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                ]);
            }
        }
    }

    public function deleted(Table $table): void
    {
        TableHistory::create([
            'table_id' => $table->id,
            'event' => 'deleted',
            'field' => null,
            'old_value' => null,
            'new_value' => null,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }
}
