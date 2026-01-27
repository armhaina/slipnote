<?php

declare(strict_types=1);

namespace App\Service\Entity;

use App\Entity\User;
use App\Exception\Entity\EntityNotFoundException;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Exception\Entity\EntityNotFoundWhenUpdateException;
use App\Model\Query\UserQueryModel;
use App\Repository\UserRepository;
use Ds\Sequence;

readonly class UserService extends AbstractService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
        parent::__construct(repository: $userRepository);
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

    /**
     * @throws EntityNotFoundWhenUpdateException
     */
    public function update(User $entity): User
    {
        if (!$entity->getId()) {
            throw new EntityNotFoundWhenUpdateException(entity: $entity::class);
        }

        return $this->userRepository->save(entity: $entity);
    }

    /**
     * @throws EntityNotFoundWhenDeleteException
     */
    public function delete(User $entity): void
    {
        if (!$entity->getId()) {
            throw new EntityNotFoundWhenDeleteException(entity: $entity::class);
        }

        $this->userRepository->delete(entity: $entity);
    }

    public function checkExistsEmail(string $email): bool
    {
        return (bool) $this->one(queryModel: new UserQueryModel()->setEmail(email: $email));
    }
}
