<?php

declare(strict_types=1);

namespace App\Exception\Entity\User;

use App\Contract\Exception\ExceptionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ForbiddenException extends AccessDeniedHttpException implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct();
    }
}
