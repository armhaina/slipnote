<?php

declare(strict_types=1);

namespace App\Enum;

enum Role: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @return array<string>
     */
    #[\Deprecated]
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
