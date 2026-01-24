<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Enum\Group;
use App\Mapper\Response\NoteResponseMapper;
use App\Model\Response\Access\ForbiddenResponseModel;
use App\Service\NoteService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $previous = $exception->getPrevious();

        $status = method_exists(
            object_or_class: $exception,
            method: 'getStatusCode'
        ) ? $exception->getStatusCode() : Response::HTTP_BAD_REQUEST;

        $data = [
            'success' => false,
            'message' => self::HTTP_STATUS_MESSAGE[$status],
            'code' => $exception->getCode(),
        ];

        if ($previous instanceof ValidationFailedException) {
            foreach ($previous->getViolations() as $violation) {
                $data['errors'][] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                    'code' => $violation->getCode(),
                ];
            }
        }

        if ($previous instanceof AccessDeniedException) {
            $data = new ForbiddenResponseModel();
        }

        $data = $this->serializer->serialize(data: $data, format: 'json', context: array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => [Group::PUBLIC->value]]));

        $event->setResponse(
            response: new JsonResponse(
                data: $data,
                status: $status,
                json: true
            )
        );
    }
}
