<?php

declare(strict_types=1);

namespace App\Model\Response\Exception;

use App\Contract\ExceptionResponseInterface;
use App\Enum\Group;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class ForbiddenResponseModelException implements ExceptionResponseInterface
{
    public function __construct(
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Подтверждение',
            type: 'boolean',
            example: false
        )]
        private bool $success = false,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
            example: 'Доступ запрещен'
        )]
        private string $message = 'Доступ запрещен',
        #[Groups([Group::ADMIN->value])]
        #[OA\Property(
            description: 'Код ошибки (только администраторы)',
            type: 'integer',
            example: 0
        )]
        private int $code = 0
    ) {
    }

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
