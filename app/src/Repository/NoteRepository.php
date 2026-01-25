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

class NoteRepository extends AbstractRepository implements RepositoryInterface
{
    public const QUERY_ALIAS = 'NoteEntity';

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct(entityClass: Note::class, registry: $registry, em: $em);
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
        $query = $this->createQueryBuilder(self::QUERY_ALIAS);

        foreach ($queryModel->getOrderBy() as $column => $order) {
            $column = $this->convertSnakeCaseToCamelCase(value: $column);
            $query->addOrderBy(sort: self::QUERY_ALIAS.'.'.$column, order: $order);
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
                ->andWhere(self::QUERY_ALIAS.'.id IN (:ids)')
            ;
        }

        if ($queryModel->getUserIds()) {
            $query
                ->setParameter('userIds', $queryModel->getUserIds())
                ->andWhere(self::QUERY_ALIAS.'.user IN (:userIds)')
            ;
        }

        if ($queryModel->getUpdatedAtLess()) {
            $query
                ->setParameter('updatedAtLess', $queryModel->getUpdatedAtLess())
                ->andWhere(self::QUERY_ALIAS.'.updatedAt < :updatedAtLess')
            ;
        }

        return $query;
    }
}
