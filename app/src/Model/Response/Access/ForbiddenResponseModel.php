<?php

declare(strict_types=1);

namespace App\Model\Response\Access;

use App\Enum\Group;
use App\Model\Response\Entity\UserResponseModel;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class ForbiddenResponseModel
{
    public function __construct(
        #[OA\Property(
            description: 'Подтверждение',
            type: 'boolean',
            example: false
        )]
        private bool $success = false,
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
            example: 'Доступ запрещен'
        )]
        private string $message = 'Доступ запрещен',
        #[OA\Property(
            description: 'Код ошибки',
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
