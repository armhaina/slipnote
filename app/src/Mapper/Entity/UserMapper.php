<?php

declare(strict_types=1);

namespace App\Mapper\Entity;

use App\Entity\User;
use App\Model\Response\Entity\UserPaginationResponseModelEntity;
use App\Model\Response\Entity\UserResponseModelEntity;
use App\Service\PaginationService;
use Knp\Component\Pager\Pagination\PaginationInterface;

readonly class UserMapper
{
    /**
     * @param array<string, mixed> $context
     */
    public function one(User $user, array $context = []): UserResponseModelEntity
    {
        return new UserResponseModelEntity(
            id: $user->getId(),
            email: $user->getEmail(),
        );
    }

    /**
     * @param array<string, mixed> $context
     * @param User[]               $users
     *
     * @return UserResponseModelEntity[]
     */
    public function collection(array $users, array $context = []): array
    {
        return array_map(
            fn (User $user): UserResponseModelEntity => $this->one(user: $user, context: $context),
            $users
        );
    }

    /**
     * @param array<string, mixed>           $context
     * @param PaginationInterface<int, User> $pagination
     */
    public function pagination(PaginationInterface $pagination, array $context = []): UserPaginationResponseModelEntity
    {
        return new UserPaginationResponseModelEntity(
            count: $pagination->count(),
            page: $pagination->getCurrentPageNumber(),
            total: $pagination->getTotalItemCount(),
            pages: PaginationService::getPages(pagination: $pagination),
            items: $this->collection(users: $pagination->getItems())
        );
    }
}
