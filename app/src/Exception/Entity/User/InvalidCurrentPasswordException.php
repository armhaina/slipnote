<?php

declare(strict_types=1);

namespace App\Exception\Entity\User;

use App\Contract\ExceptionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidCurrentPasswordException extends BadRequestHttpException implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct(message: 'Неверный текущий пароль пользователя');
    }
}
