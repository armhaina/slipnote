<?php

declare(strict_types=1);

namespace App\Model\Response\Entity;

use App\Enum\Group;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class UserResponseModelEntity
{
    public function __construct(
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Уникальный идентификатор пользователя',
            type: 'integer',
        )]
        private int $id,
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Email пользователя',
            type: 'string',
        )]
        private string $email,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
