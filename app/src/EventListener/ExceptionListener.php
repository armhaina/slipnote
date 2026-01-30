<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Contract\Exception\ExceptionResponseInterface;
use App\Enum\Group;
use App\Enum\Role;
use App\Message\HttpStatusMessage;
use App\Model\Response\Exception\ContextResponseModelException;
use App\Model\Response\Exception\DefaultResponseModelException;
use App\Model\Response\Exception\ForbiddenResponseModelException;
use App\Model\Response\Exception\ValidationResponseModelException;
use App\Model\Response\Exception\ViolationResponseModelException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

readonly class ExceptionListener
{
    public function __construct(
        private SerializerInterface $serializer,
        private Security $security,
        private ClassMetadataFactoryInterface $classMetadataFactory
    ) {}

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
        $previous = $exception->getPrevious();

        // TODO: переделать на $previous
        if ($exception instanceof AccessDeniedHttpException) {
            return $this->forbiddenExceptionHandler(exception: $exception, status: $status);
        }

        if ($previous instanceof ValidationFailedException) {
            return $this->validationExceptionHandler(exception: $exception, status: $status);
        }

        return new DefaultResponseModelException(
            success: false,
            message: $this->isUserException(exception: $exception) ? $exception->getMessage(
            ) : HttpStatusMessage::HTTP_STATUS_MESSAGE[$status],
            context: new ContextResponseModelException(
                file: $exception->getFile(),
                line: $exception->getLine(),
                message: $exception->getMessage(),
            )
        );
    }

    private function forbiddenExceptionHandler(\Throwable $exception, int $status): ExceptionResponseInterface
    {
        return new ForbiddenResponseModelException(
            success: false,
            message: HttpStatusMessage::HTTP_STATUS_MESSAGE[$status],
            code: $exception->getCode(),
        );
    }

    private function validationExceptionHandler(\Throwable $exception, int $status): ExceptionResponseInterface
    {
        $errors = [];

        $previous = $exception->getPrevious() ?? null;

        if ($previous instanceof ValidationFailedException) {
            foreach ($previous->getViolations() as $violation) {
                // region Получить сериализованное имя (если оно есть)
                $attributes = $this->classMetadataFactory->getMetadataFor(value: $previous->getValue()::class);

                // Убрать квадратные скобки с цифрами внутри: userIds[0] => userIds
                $propertyPath = preg_replace(
                    pattern: '/\[\d+\]$/',
                    replacement: '',
                    subject: $violation->getPropertyPath()
                );

                $serializedName = $attributes->getAttributesMetadata()[$propertyPath]->getSerializedName(
                ) ?? $violation->getPropertyPath();
                // endregion

                $errors[] = new ViolationResponseModelException(
                    property: $serializedName,
                    message: $violation->getMessage(),
                    code: $violation->getCode()
                );
            }
        }

        return new ValidationResponseModelException(
            success: false,
            message: HttpStatusMessage::HTTP_STATUS_MESSAGE[$status],
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

    private function isUserException(\Throwable $exception): bool
    {
        if ($exception instanceof \App\Contract\Exception\ExceptionInterface) {
            return true;
        }

        return false;
    }
}
