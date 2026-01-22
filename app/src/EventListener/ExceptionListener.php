<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    private const array HTTP_STATUS_MESSAGE = [
        Response::HTTP_BAD_REQUEST => 'Некорректный запрос',
        Response::HTTP_UNAUTHORIZED => 'Требуется авторизация',
        Response::HTTP_FORBIDDEN => 'Доступ запрещен',
        Response::HTTP_NOT_FOUND => 'Ресурс не найден',
        Response::HTTP_METHOD_NOT_ALLOWED => 'Метод не разрешен',
        Response::HTTP_UNPROCESSABLE_ENTITY => 'Ошибка валидации',
        Response::HTTP_TOO_MANY_REQUESTS => 'Слишком много запросов',
        Response::HTTP_INTERNAL_SERVER_ERROR => 'Внутренняя ошибка сервера',
        Response::HTTP_SERVICE_UNAVAILABLE => 'Сервис временно недоступен',
    ];

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $data = [
            'success' => false,
            'message' => self::HTTP_STATUS_MESSAGE[method_exists(
                object_or_class: $exception,
                method: 'getStatusCode'
            ) ? $exception->getStatusCode() : Response::HTTP_BAD_REQUEST],
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
