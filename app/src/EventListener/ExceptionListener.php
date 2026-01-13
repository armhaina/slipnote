<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $data = [
            'error' => true,
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];

        if (!empty($exception->getPrevious())) {
            $data['previous'] = [
                'message' => $exception->getPrevious()->getMessage(),
            ];
        }

        $response = new JsonResponse(
            data: $data,
            status: method_exists(
                object_or_class: $exception,
                method: 'getStatusCode'
            ) ? $exception->getStatusCode() : 400
        );

        $event->setResponse(response: $response);
    }
}
