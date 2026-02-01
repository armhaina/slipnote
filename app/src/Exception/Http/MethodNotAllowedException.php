<?php

declare(strict_types=1);

namespace App\Exception\Http;

use App\Contract\ExceptionInterface;
use App\Enum\Message\HttpStatusMessage;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class MethodNotAllowedException extends MethodNotAllowedHttpException implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct(allow: [], message: HttpStatusMessage::HTTP_METHOD_NOT_ALLOWED->value);
    }
}
