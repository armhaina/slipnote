<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\EntityInterface;
use App\Contract\EntityQueryModelInterface;
use App\Contract\ServiceInterface;
use App\Entity\Note;
use App\Exception\Entity\EntityInvalidObjectTypeException;
use App\Exception\Entity\EntityNotFoundException;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Exception\Entity\EntityNotFoundWhenUpdateException;
use App\Exception\EntityModel\EntityModelInvalidObjectTypeException;
use App\Exception\EntityQueryModel\EntityQueryModelInvalidObjectTypeException;
use App\Model\Query\NoteQueryModel;
use App\Repository\NoteRepository;
use Ds\Sequence;

readonly class NoteService extends AbstractService implements ServiceInterface
{
    public function __construct(
        private NoteRepository $noteRepository,
    ) {
        parent::__construct(repository: $noteRepository);
    }

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    public function count(EntityQueryModelInterface $queryModel): int
    {
        if (!$queryModel instanceof NoteQueryModel) {
            throw new EntityQueryModelInvalidObjectTypeException();
        }

        $criteria = [];

        return $this->noteRepository->count(criteria: $criteria);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(int $id): Note
    {
        $entity = $this->noteRepository->get(id: $id);

        if (!$entity) {
            throw new EntityNotFoundException(entity: Note::class, id: $id);
        }

        return $entity;
    }

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    public function one(EntityQueryModelInterface $queryModel): ?Note
    {
        if (!$queryModel instanceof NoteQueryModel) {
            throw new EntityQueryModelInvalidObjectTypeException();
        }

        return $this->noteRepository->one(queryModel: $queryModel);
    }

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    public function list(EntityQueryModelInterface $queryModel): Sequence
    {
        if (!$queryModel instanceof NoteQueryModel) {
            throw new EntityQueryModelInvalidObjectTypeException();
        }

        return $this->noteRepository->list(queryModel: $queryModel);
    }

    /**
     * @throws EntityInvalidObjectTypeException
     * @throws EntityModelInvalidObjectTypeException
     */
    public function create(EntityInterface $entity): Note
    {
        if (!$entity instanceof Note) {
            throw new EntityModelInvalidObjectTypeException();
        }

        return $this->noteRepository->save(entity: $entity);
    }

    /**
     * @throws EntityInvalidObjectTypeException
     * @throws EntityModelInvalidObjectTypeException
     * @throws EntityNotFoundWhenUpdateException
     */
    public function update(EntityInterface $entity): Note
    {
        if (!$entity instanceof Note) {
            throw new EntityModelInvalidObjectTypeException();
        }

        if (!$entity->getId()) {
            throw new EntityNotFoundWhenUpdateException(entity: $entity::class);
        }

        return $this->noteRepository->save(entity: $entity);
    }

    /**
     * @throws EntityInvalidObjectTypeException
     * @throws EntityModelInvalidObjectTypeException
     * @throws EntityNotFoundWhenDeleteException
     */
    public function delete(EntityInterface $entity): void
    {
        if (!$entity instanceof Note) {
            throw new EntityModelInvalidObjectTypeException();
        }

        if (!$entity->getId()) {
            throw new EntityNotFoundWhenDeleteException(entity: $entity::class);
        }

        $this->noteRepository->delete(entity: $entity);
    }
}
