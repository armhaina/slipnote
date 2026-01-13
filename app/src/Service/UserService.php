<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\EntityInterface;
use App\Contract\EntityQueryModelInterface;
use App\Contract\ServiceInterface;
use App\Entity\User;
use App\Exception\Entity\EntityInvalidObjectTypeException;
use App\Exception\Entity\EntityNotFoundException;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Exception\Entity\EntityNotFoundWhenUpdateException;
use App\Exception\EntityModel\EntityModelInvalidObjectTypeException;
use App\Exception\EntityQueryModel\EntityQueryModelInvalidObjectTypeException;
use App\Model\Query\UserQueryModel;
use App\Repository\UserRepository;
use Ds\Sequence;

readonly class UserService extends AbstractService implements ServiceInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
        parent::__construct(repository: $userRepository);
    }

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    public function count(EntityQueryModelInterface $queryModel): int
    {
        if (!$queryModel instanceof UserQueryModel) {
            throw new EntityQueryModelInvalidObjectTypeException();
        }

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

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    public function one(EntityQueryModelInterface $queryModel): ?User
    {
        if (!$queryModel instanceof UserQueryModel) {
            throw new EntityQueryModelInvalidObjectTypeException();
        }

        return $this->userRepository->one(queryModel: $queryModel);
    }

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    public function list(EntityQueryModelInterface $queryModel): Sequence
    {
        if (!$queryModel instanceof UserQueryModel) {
            throw new EntityQueryModelInvalidObjectTypeException();
        }

        return $this->userRepository->list(queryModel: $queryModel);
    }

    /**
     * @throws EntityInvalidObjectTypeException
     * @throws EntityModelInvalidObjectTypeException
     */
    public function create(EntityInterface $entity): User
    {
        if (!$entity instanceof User) {
            throw new EntityModelInvalidObjectTypeException();
        }

        return $this->userRepository->save(entity: $entity);
    }

    /**
     * @throws EntityInvalidObjectTypeException
     * @throws EntityModelInvalidObjectTypeException
     * @throws EntityNotFoundWhenUpdateException
     */
    public function update(EntityInterface $entity): User
    {
        if (!$entity instanceof User) {
            throw new EntityModelInvalidObjectTypeException();
        }

        if (!$entity->getId()) {
            throw new EntityNotFoundWhenUpdateException(entity: $entity::class);
        }

        return $this->userRepository->save(entity: $entity);
    }

    /**
     * @throws EntityInvalidObjectTypeException
     * @throws EntityModelInvalidObjectTypeException
     * @throws EntityNotFoundWhenDeleteException
     */
    public function delete(EntityInterface $entity): void
    {
        if (!$entity instanceof User) {
            throw new EntityModelInvalidObjectTypeException();
        }

        if (!$entity->getId()) {
            throw new EntityNotFoundWhenDeleteException(entity: $entity::class);
        }

        $this->userRepository->delete(entity: $entity);
    }
}
