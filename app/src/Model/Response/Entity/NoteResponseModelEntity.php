<?php

declare(strict_types=1);

namespace App\Model\Response\Entity;

use App\Enum\Entity\User\GroupUser;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class NoteResponseModelEntity
{
    public function __construct(
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Уникальный идентификатор заметки',
            type: 'integer',
        )]
        private int $id,
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Наименование',
            type: 'string',
        )]
        private string $name,
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Описание',
            type: 'string',
        )]
        private string $description,
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Корзина',
            type: 'boolean',
        )]
        #[SerializedName(serializedName: 'is_trashed')]
        private bool $isTrashed,
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Пользователь (владелец заметки)',
        )]
        private UserResponseModelEntity $user,
    ) {}

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

    public function getIsTrashed(): bool
    {
        return $this->isTrashed;
    }

    public function getUser(): UserResponseModelEntity
    {
        return $this->user;
    }
}
