<?php

declare(strict_types=1);

namespace App\Model\Response\Action;

use App\Enum\Group;
use App\Model\Response\Entity\UserResponseModel;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class DeleteResponseModel
{
    public function __construct(
        #[OA\Property(
            description: 'Подтверждение',
            type: 'boolean',
            example: true
        )]
        private bool $success = true,
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
            example: 'Запись успешно удалена'
        )]
        private string $message = 'Запись успешно удалена'
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
}
