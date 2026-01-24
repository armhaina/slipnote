<?php

declare(strict_types=1);

namespace App\Model\Response\Exception;

use App\Contract\ExceptionResponseInterface;
use App\Enum\Group;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class ViolationResponseModelException implements ExceptionResponseInterface
{
    public function __construct(
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Свойство',
            type: 'string',
        )]
        private string $property,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
        )]
        private string $message,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Код ошибки',
            type: 'string',
        )]
        private ?string $code,
    ) {
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }
}
