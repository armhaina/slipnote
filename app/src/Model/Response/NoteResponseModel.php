<?php

declare(strict_types=1);

namespace App\Model\Response;

use App\Enum\Group;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

readonly class NoteResponseModel
{
    public function __construct(
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Уникальный идентификатор заметки',
            type: 'integer',
            example: 1
        )]
        private int $id,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Наименование',
            type: 'string',
            example: 'Первая заметка'
        )]
        private string $name,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Описание',
            type: 'string',
            example: 'Первая описание заметки'
        )]
        private string $description,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Пользователь (владелец заметки)',
        )]
        private UserResponseModel $user,
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

    public function getUser(): UserResponseModel
    {
        return $this->user;
    }
}
