<?php

declare(strict_types=1);

namespace App\Model\Response\Exception;

use App\Contract\Exception\ExceptionResponseInterface;
use App\Enum\Group;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class DefaultResponseModelException implements ExceptionResponseInterface
{
    public function __construct(
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Статус',
            type: 'boolean',
            example: false
        )]
        private bool $success,
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
            example: 'Ошибка'
        )]
        private string $message,
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
