<?php

declare(strict_types=1);

namespace App\Model\Response\Exception;

use App\Enum\Entity\User\GroupUser;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class DefaultResponseModelException
{
    /**
     * @param ViolationResponseModelException[] $violations
     */
    public function __construct(
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Статус',
            type: 'boolean',
        )]
        private bool $success,
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Код ошибки',
            type: 'integer',
        )]
        private int $code,
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
        )]
        private string $message,
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Нарушения',
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: ViolationResponseModelException::class,
                )
            ),
            nullable: true
        )]
        private array $violations,
        #[Groups([GroupUser::ADMIN->value])]
        #[OA\Property(
            ref: new Model(
                type: ContextResponseModelException::class,
            ),
            description: 'Контекст ('.GroupUser::ADMIN->value.')',
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
