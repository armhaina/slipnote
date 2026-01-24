<?php

declare(strict_types=1);

namespace App\Message;

use App\Contract\Entity\EntityInterface;
use App\Contract\Entity\EntityQueryModelInterface;
use App\Contract\ServiceInterface;
use App\Entity\Note;
use App\Exception\Entity\EntityInvalidObjectTypeException;
use App\Exception\Entity\EntityNotFoundException;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Exception\Entity\EntityNotFoundWhenUpdateException;
use App\Exception\EntityModel\EntityModelInvalidObjectTypeException;
use App\Exception\EntityQueryModel\EntityQueryModelInvalidObjectTypeException;
use App\Model\Query\NoteQueryModel;
use App\Repository\NoteRepository;
use App\Service\AbstractService;
use Ds\Sequence;
use Symfony\Component\HttpFoundation\Response;

readonly class HttpStatusMessage
{
    public const array HTTP_STATUS_MESSAGE = [
        Response::HTTP_OK => 'Успех',
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
}
