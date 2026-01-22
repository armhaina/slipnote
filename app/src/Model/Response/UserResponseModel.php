<?php

declare(strict_types=1);

namespace App\Model\Response;

use App\Enum\Group;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

readonly class UserResponseModel
{
    public function __construct(
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Уникальный идентификатор пользователя',
            type: 'integer',
            example: 1
        )]
        private int $id,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Email пользователя',
            type: 'string',
            example: 'example@mail.ru'
        )]
        private string $email,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
