<?php

declare(strict_types=1);

namespace App\EventListener\Exception;

use App\Entity\User;
use App\Enum\Message\HttpStatusMessage;
use App\Model\Response\Exception\AllResponseModelException;
use App\Model\Response\Exception\ContextResponseModelException;
use App\Model\Response\Exception\ViolationResponseModelException;
use App\Service\Entity\UserService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

abstract readonly class AbstractExceptionListener
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected Security $security,
        protected ClassMetadataFactoryInterface $classMetadataFactory
    ) {}

    /**
     * @throws ExceptionInterface
     */
    protected function serialize(AllResponseModelException $model): string
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $groups = UserService::getGroupsByUserRoles(user: $user);

        return $this->serializer->serialize(
            data: $model,
            format: 'json',
            context: array_merge(
                ['json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS],
                ['groups' => $groups]
            )
        );
    }

    protected function exceptionFactory(\Throwable $exception, int $status): AllResponseModelException
    {
        $violations = [];

        if ($exception instanceof UnprocessableEntityHttpException) {
            $violations = $this->violationsExceptionHandler(exception: $exception);
        }

        return new AllResponseModelException(
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
        if ($exception instanceof \App\Contract\ExceptionInterface) {
            return $exception->getMessage();
        }

        return HttpStatusMessage::getValue(code: $status);
    }
}
