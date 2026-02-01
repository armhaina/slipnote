<?php

declare(strict_types=1);

namespace App\Exception\Auth;

use App\Contract\ExceptionInterface;
use App\Enum\Message\HttpStatusMessage;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ForbiddenException extends AccessDeniedHttpException implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct(message: HttpStatusMessage::HTTP_FORBIDDEN->value);
    }
}
