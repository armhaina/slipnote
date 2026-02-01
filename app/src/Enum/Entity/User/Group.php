<?php

declare(strict_types=1);

namespace App\Enum\Entity\User;

enum Group: string
{
    case PUBLIC = 'PUBLIC';
    case ADMIN = 'ADMIN';
}
