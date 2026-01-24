<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Contract\ExceptionResponseInterface;
use App\Enum\Group;
use App\Enum\Role;
use App\Model\Response\Exception\DefaultResponseModelException;
use App\Model\Response\Exception\ForbiddenResponseModelException;
use App\Model\Response\Exception\ValidationResponseModelException;
use App\Model\Response\Exception\ViolationResponseModelException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

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
        private readonly Security $security
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $status = method_exists(
            object_or_class: $exception,
            method: 'getStatusCode'
        ) ? $exception->getStatusCode() : Response::HTTP_BAD_REQUEST;

        $data = $this->exceptionFactory(exception: $exception, status: $status);
        $groups = $this->getGroupsByUserRoles();

        $data = $this->serializer->serialize(data: $data, format: 'json', context: array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => $groups]));

        $event->setResponse(
            response: new JsonResponse(
                data: $data,
                status: $status,
                json: true
            )
        );
    }

    private function exceptionFactory(\Throwable $exception, int $status): ExceptionResponseInterface
    {
        if ($exception instanceof AccessDeniedHttpException) {
            return $this->forbiddenExceptionHandler(exception: $exception, status: $status);
        }

        if ($exception instanceof UnprocessableEntityHttpException) {
            return $this->validationExceptionHandler(exception: $exception, status: $status);
        }

        return new DefaultResponseModelException(success: false, message: self::HTTP_STATUS_MESSAGE[$status]);
    }

    private function forbiddenExceptionHandler(\Throwable $exception, int $status): ExceptionResponseInterface
    {
        return new ForbiddenResponseModelException(
            success: false,
            message: self::HTTP_STATUS_MESSAGE[$status],
            code: $exception->getCode(),
        );
    }

    private function validationExceptionHandler(\Throwable $exception, int $status): ExceptionResponseInterface
    {
        $errors = [];

        $previous = $exception->getPrevious() ?? null;

        if ($previous instanceof ValidationFailedException) {
            foreach ($previous->getViolations() as $violation) {
                $errors[] = new ViolationResponseModelException(
                    property: $violation->getPropertyPath(),
                    message: $violation->getMessage(),
                    code: $violation->getCode()
                );
            }
        }

        return new ValidationResponseModelException(
            success: false,
            message: self::HTTP_STATUS_MESSAGE[$status],
            code: $exception->getCode(),
            errors: $errors,
        );
    }

    /**
     * @return string[]
     */
    private function getGroupsByUserRoles(): array
    {
        $user = $this->security->getUser() ?? null;

        $groups = [Group::PUBLIC->value];

        if (!$user) {
            return $groups;
        }

        if (in_array(needle: Role::ROLE_ADMIN->value, haystack: $user->getRoles())) {
            $groups[] = Group::ADMIN->value;
        }

        return $groups;
    }
}
