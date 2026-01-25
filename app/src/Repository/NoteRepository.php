<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\RepositoryInterface;
use App\Entity\Note;
use App\Model\Query\NoteQueryModel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Ds\Vector;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class NoteRepository extends AbstractRepository implements RepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ) {
        parent::__construct(entityClass: Note::class, registry: $registry, em: $em, paginator: $paginator);
    }

    public function get(int $id): ?Note
    {
        return $this->find($id);
    }

    public function one(NoteQueryModel $queryModel): ?Note
    {
        $queryBuilder = $this->queryBuilder(queryModel: $queryModel)->setMaxResults(maxResults: 1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Vector<Note>
     */
    public function list(NoteQueryModel $queryModel): Vector
    {
        $queryBuilder = $this->queryBuilder(queryModel: $queryModel);

        return new Vector($queryBuilder->getQuery()->getResult());
    }

    /**
     * @return PaginationInterface<int, Note>
     */
    public function pagination(NoteQueryModel $queryModel): PaginationInterface
    {
        $queryBuilder = $this->queryBuilder(queryModel: $queryModel);

        return $this->paginator->paginate(
            target: $queryBuilder->getQuery(),
            page: $queryModel->getOffset(),
            limit: $queryModel->getLimit()
        );
    }

    public function save(Note $entity): Note
    {
        $this->em->persist(object: $entity);
        $this->em->flush();

        return $entity;
    }

    public function delete(Note $entity): void
    {
        $this->em->remove(object: $entity);
        $this->em->flush();
    }

    private function queryBuilder(NoteQueryModel $queryModel): QueryBuilder
    {
        $query = $this->createQueryBuilder(Note::shortName());

        foreach ($queryModel->getOrderBy() as $column => $order) {
            $column = $this->convertSnakeCaseToCamelCase(value: $column);
            $query->addOrderBy(sort: Note::shortName().'.'.$column, order: $order);
        }

        if (!empty($queryModel->getOffset())) {
            $query->setFirstResult($queryModel->getOffset());
        }

        if (!empty($queryModel->getLimit())) {
            $query->setMaxResults($queryModel->getLimit());
        }

        if ($queryModel->getIds()) {
            $query
                ->setParameter('ids', $queryModel->getIds())
                ->andWhere(Note::shortName().'.id IN (:ids)')
            ;
        }

        if ($queryModel->getUserIds()) {
            $query
                ->setParameter('userIds', $queryModel->getUserIds())
                ->andWhere(Note::shortName().'.user IN (:userIds)')
            ;
        }

        if ($queryModel->getUpdatedAtLess()) {
            $query
                ->setParameter('updatedAtLess', $queryModel->getUpdatedAtLess())
                ->andWhere(Note::shortName().'.updatedAt < :updatedAtLess')
            ;
        }

        return $query;
    }
}
