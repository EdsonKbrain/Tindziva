<?php

namespace App\Enums;

enum ClientType: string
{
    case Client = 'client';
    case Partner = 'partner';

    public function label(): string
    {
        return match ($this) {
            self::Client => 'Cliente',
            self::Partner => 'Parceiro',
        };
    }
}
