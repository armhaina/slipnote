<?php

declare(strict_types=1);

namespace App\Model\Response\Exception;

use App\Contract\Exception\ExceptionResponseInterface;
use App\Enum\Entity\User\GroupUser;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class ForbiddenResponseModelException implements ExceptionResponseInterface
{
    public function __construct(
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Статус',
            type: 'boolean',
        )]
        private bool $success,
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
        )]
        private string $message,
        #[Groups([GroupUser::ADMIN->value])]
        #[OA\Property(
            description: 'Код ошибки ('.GroupUser::ADMIN->value.')',
            type: 'integer',
            nullable: true,
        )]
        private int $code
    ) {}

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}
