<?php

namespace App\Enums;

enum UserRole: string
{
    case Manager = 'manager';
    case Waiter = 'waiter';
    case Kitchen = 'kitchen';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Manager => 'Gerente',
            self::Waiter => 'Garçom',
            self::Kitchen => 'Cozinha',
            self::Customer => 'Cliente',
        };
    }
}
