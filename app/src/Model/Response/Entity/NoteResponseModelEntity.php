<?php

declare(strict_types=1);

namespace App\Model\Response\Entity;

use App\Enum\Group;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class NoteResponseModelEntity
{
    public function __construct(
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Уникальный идентификатор заметки',
            type: 'integer',
        )]
        private int $id,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Наименование',
            type: 'string',
        )]
        private string $name,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Описание',
            type: 'string',
        )]
        private string $description,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Пользователь (владелец заметки)',
        )]
        private UserResponseModelEntity $user,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUser(): UserResponseModelEntity
    {
        return $this->user;
    }
}
