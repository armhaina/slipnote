<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Contract\Exception\ExceptionResponseInterface;
use App\Entity\User;
use App\Message\HttpStatusMessage;
use App\Model\Response\Exception\ContextResponseModelException;
use App\Model\Response\Exception\DefaultResponseModelException;
use App\Model\Response\Exception\ViolationResponseModelException;
use App\Service\Entity\UserService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
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

        /** @var User $user */
        $user = $this->security->getUser();

        $groups = UserService::getGroupsByUserRoles(user: $user);

        $data = $this->serializer->serialize(
            data: $data,
            format: 'json',
            context: array_merge(
                ['json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS],
                ['groups' => $groups]
            )
        );

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
        $violations = [];

        if ($exception instanceof UnprocessableEntityHttpException) {
            $violations = $this->violationsExceptionHandler(exception: $exception);
        }

        return new DefaultResponseModelException(
            success: false,
            code: $exception->getCode(),
            message: $this->getMessage(exception: $exception, status: $status),
            violations: $violations,
            context: new ContextResponseModelException(
                file: $exception->getFile(),
                line: $exception->getLine(),
                message: $exception->getMessage(),
            )
        );
    }

    /**
     * @return array<ViolationResponseModelException>
     */
    private function violationsExceptionHandler(\Throwable $exception): array
    {
        $violations = [];

        $previous = $exception->getPrevious();

        if ($previous instanceof ValidationFailedException) {
            foreach ($previous->getViolations() as $violation) {
                // region Получить сериализованное имя (если оно есть)
                $attributes = $this->classMetadataFactory->getMetadataFor(value: $previous->getValue()::class);

                // Убрать квадратные скобки с цифрами внутри: userIds[0] => userIds
                $propertyPath = preg_replace(
                    pattern: '/\[\d+]$/',
                    replacement: '',
                    subject: $violation->getPropertyPath()
                );

                $serializedName = $attributes->getAttributesMetadata()[$propertyPath]->getSerializedName(
                ) ?? $violation->getPropertyPath();
                // endregion

                $violations[] = new ViolationResponseModelException(
                    property: $serializedName,
                    message: $violation->getMessage(),
                    code: $violation->getCode()
                );
            }
        }

        return $violations;
    }

    private function getMessage(\Throwable $exception, int $status): string
    {
        if ($exception instanceof \App\Contract\Exception\ExceptionInterface) {
            return $exception->getMessage();
        }

        return HttpStatusMessage::HTTP_STATUS_MESSAGE[$status];
    }
}
