<?php

declare(strict_types=1);

namespace App\Model\Response\Entity;

use App\Enum\Entity\User\Group;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class UserPaginationResponseModelEntity
{
    /**
     * @param array<int, UserResponseModelEntity> $items
     */
    public function __construct(
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Количество записей на странице',
            type: 'integer',
        )]
        private int $count,
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Номер страницы',
            type: 'integer',
        )]
        private int $page,
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Количество записей',
            type: 'integer',
        )]
        private int $total,
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Количество страниц',
            type: 'integer',
        )]
        private int $pages,
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Пользователи',
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: UserResponseModelEntity::class,
                )
            )
        )]
        private array $items,
    ) {}

    public function getCount(): int
    {
        return $this->count;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    /**
     * @return array<UserResponseModelEntity>
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
