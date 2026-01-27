<?php

declare(strict_types=1);

namespace App\Service\Entity;

use App\Entity\Note;
use App\Exception\Entity\EntityNotFoundException;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Exception\Entity\EntityNotFoundWhenUpdateException;
use App\Model\Query\NoteQueryModel;
use App\Repository\NoteRepository;
use Ds\Sequence;
use Knp\Component\Pager\Pagination\PaginationInterface;

readonly class NoteService extends AbstractService
{
    public function __construct(
        private NoteRepository $noteRepository,
    ) {
        parent::__construct(repository: $noteRepository);
    }

    public function count(NoteQueryModel $queryModel): int
    {
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

    public function one(NoteQueryModel $queryModel): ?Note
    {
        return $this->noteRepository->one(queryModel: $queryModel);
    }

    /**
     * @return PaginationInterface<int, Note>
     */
    public function pagination(NoteQueryModel $queryModel): PaginationInterface
    {
        return $this->noteRepository->pagination(queryModel: $queryModel);
    }

    /**
     * @return Sequence<Note>
     */
    public function list(NoteQueryModel $queryModel): Sequence
    {
        return $this->noteRepository->list(queryModel: $queryModel);
    }

    public function create(Note $entity): Note
    {
        return $this->noteRepository->save(entity: $entity);
    }

    /**
     * @throws EntityNotFoundWhenUpdateException
     */
    public function update(Note $entity): Note
    {
        if (!$entity->getId()) {
            throw new EntityNotFoundWhenUpdateException(entity: $entity::class);
        }

        return $this->noteRepository->save(entity: $entity);
    }

    /**
     * @throws EntityNotFoundWhenDeleteException
     */
    public function delete(Note $entity): void
    {
        if (!$entity->getId()) {
            throw new EntityNotFoundWhenDeleteException(entity: $entity::class);
        }

        $this->noteRepository->delete(entity: $entity);
    }
}
