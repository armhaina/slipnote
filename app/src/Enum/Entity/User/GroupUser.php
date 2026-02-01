<?php

declare(strict_types=1);

namespace App\Enum\Entity\User;

enum GroupUser: string
{
    case PUBLIC = 'PUBLIC';
    case ADMIN = 'ADMIN';
}
