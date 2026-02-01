<?php

declare(strict_types=1);

namespace App\Enum\Message;

enum HttpStatusMessage: string
{
    case HTTP_OK = 'Успех';
    case HTTP_BAD_REQUEST = 'Некорректный запрос';
    case HTTP_UNAUTHORIZED = 'Требуется авторизация';
    case HTTP_FORBIDDEN = 'Доступ запрещен';
    case HTTP_NOT_FOUND = 'Ресурс не найден';
    case HTTP_METHOD_NOT_ALLOWED = 'Метод не разрешен';
    case HTTP_CONFLICT = 'Конфликт данных';
    case HTTP_UNPROCESSABLE_ENTITY = 'Ошибка валидации';
    case HTTP_TOO_MANY_REQUESTS = 'Слишком много запросов';
    case HTTP_INTERNAL_SERVER_ERROR = 'Внутренняя ошибка сервера';
    case HTTP_SERVICE_UNAVAILABLE = 'Сервис временно недоступен';

    public static function getValue(int $code): string
    {
        return match ($code) {
            200 => self::HTTP_OK->value,
            400 => self::HTTP_BAD_REQUEST->value,
            401 => self::HTTP_UNAUTHORIZED->value,
            403 => self::HTTP_FORBIDDEN->value,
            404 => self::HTTP_NOT_FOUND->value,
            405 => self::HTTP_METHOD_NOT_ALLOWED->value,
            409 => self::HTTP_CONFLICT->value,
            422 => self::HTTP_UNPROCESSABLE_ENTITY->value,
            429 => self::HTTP_TOO_MANY_REQUESTS->value,
            503 => self::HTTP_SERVICE_UNAVAILABLE->value,
            default => self::HTTP_INTERNAL_SERVER_ERROR->value,
        };
    }
}
