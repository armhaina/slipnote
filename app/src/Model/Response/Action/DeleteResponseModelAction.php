<?php

declare(strict_types=1);

namespace App\Model\Response\Action;

use App\Enum\Group;
use App\Model\Response\Entity\UserResponseModelEntity;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class DeleteResponseModelAction
{
    public function __construct(
        #[OA\Property(
            description: 'Статус',
            type: 'boolean',
        )]
        private bool $success = true,
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
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
