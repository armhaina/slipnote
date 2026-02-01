<?php

declare(strict_types=1);

namespace App\Service\Entity;

use App\Entity\User;
use App\Enum\Entity\User\GroupUser;
use App\Enum\Entity\User\RoleUser;
use App\Exception\Entity\EntityNotFoundException;
use App\Model\Query\UserQueryModel;
use App\Repository\UserRepository;
use Ds\Sequence;

readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function transaction(callable $func): void
    {
        $this->userRepository->transaction(func: $func);
    }

    public function count(UserQueryModel $queryModel): int
    {
        $criteria = [];

        return $this->userRepository->count(criteria: $criteria);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(int $id): User
    {
        $entity = $this->userRepository->get(id: $id);

        if (!$entity) {
            throw new EntityNotFoundException(entity: User::class, id: $id);
        }

        return $entity;
    }

    public function one(UserQueryModel $queryModel): ?User
    {
        return $this->userRepository->one(queryModel: $queryModel);
    }

    /**
     * @return Sequence<User>
     */
    public function list(UserQueryModel $queryModel): Sequence
    {
        return $this->userRepository->list(queryModel: $queryModel);
    }

    public function create(User $entity): User
    {
        return $this->userRepository->save(entity: $entity);
    }

    public function update(User $entity): User
    {
        if (!$entity->getId()) {
            throw new EntityNotFoundException(entity: $entity::class);
        }

        return $this->userRepository->save(entity: $entity);
    }

    public function delete(User $entity): void
    {
        if (!$entity->getId()) {
            throw new EntityNotFoundException(entity: $entity::class);
        }

        $this->userRepository->delete(entity: $entity);
    }

    public function checkExistsEmail(string $email): bool
    {
        return (bool) $this->one(queryModel: new UserQueryModel()->setEmail(email: $email));
    }

    /**
     * @return string[]
     */
    public static function getGroupsByUserRoles(?User $user): array
    {
        $groups = [GroupUser::PUBLIC->value];

        if (!$user) {
            return $groups;
        }

        if (in_array(needle: RoleUser::ROLE_ADMIN->value, haystack: $user->getRoles())) {
            $groups[] = GroupUser::ADMIN->value;
        }

        return $groups;
    }
}
