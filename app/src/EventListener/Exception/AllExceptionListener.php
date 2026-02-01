<?php

declare(strict_types=1);

namespace App\EventListener\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

readonly class AllExceptionListener extends AbstractExceptionListener
{
    /**
     * @throws ExceptionInterface
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $status = method_exists(
            object_or_class: $exception,
            method: 'getStatusCode'
        ) ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        $model = $this->exceptionFactory(exception: $exception, status: $status);
        $data = $this->serialize(model: $model);

        $event->setResponse(
            response: new JsonResponse(
                data: $data,
                status: $status,
                json: true
            )
        );
    }
}
