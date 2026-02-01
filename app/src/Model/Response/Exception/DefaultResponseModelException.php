<?php

declare(strict_types=1);

namespace App\Model\Response\Exception;

use App\Contract\Exception\ExceptionResponseInterface;
use App\Enum\Entity\User\Group;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class DefaultResponseModelException implements ExceptionResponseInterface
{
    /**
     * @param ViolationResponseModelException[] $violations
     */
    public function __construct(
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Статус',
            type: 'boolean',
        )]
        private bool $success,
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Код ошибки',
            type: 'integer',
        )]
        private int $code,
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
        )]
        private string $message,
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Нарушения',
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: ViolationResponseModelException::class,
                )
            )
        )]
        private array $violations,
        #[Groups([Group::ADMIN->value])]
        #[OA\Property(
            ref: new Model(
                type: ContextResponseModelException::class,
            ),
            description: 'Контекст ('.Group::ADMIN->value.')',
            type: 'object',
            nullable: true
        )]
        private ContextResponseModelException $context,
    ) {}

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getContext(): ContextResponseModelException
    {
        return $this->context;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return array<ViolationResponseModelException>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
