<?php

declare(strict_types=1);

namespace App\Enum\Message;

enum HttpStatusMessageNew: string
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
}
