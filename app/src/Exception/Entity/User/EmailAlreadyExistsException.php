<?php

declare(strict_types=1);

namespace App\Exception\Entity\User;

use App\Contract\Exception\ExceptionInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class EmailAlreadyExistsException extends ConflictHttpException implements ExceptionInterface
{
    public function __construct(string $email)
    {
        parent::__construct(message: "Пользователь с почтой {$email} уже существует");
    }
}
