<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\RepositoryInterface;
use App\Entity\User;
use App\Model\Query\UserQueryModel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Ds\Vector;

class UserRepository extends AbstractRepository implements RepositoryInterface
{
    public const QUERY_ALIAS = 'UserEntity';

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct(entityClass: User::class, registry: $registry, em: $em);
    }

    public function get(int $id): ?User
    {
        return $this->find($id);
    }

    public function one(UserQueryModel $queryModel): ?User
    {
        $queryBuilder = $this->queryBuilder(queryModel: $queryModel)->setMaxResults(maxResults: 1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Vector<User>
     */
    public function list(UserQueryModel $queryModel): Vector
    {
        $queryBuilder = $this->queryBuilder(queryModel: $queryModel);

        return new Vector($queryBuilder->getQuery()->getResult());
    }

    public function save(User $entity): User
    {
        $this->em->persist(object: $entity);
        $this->em->flush();

        return $entity;
    }

    public function delete(User $entity): void
    {
        $this->em->remove(object: $entity);
        $this->em->flush();
    }

    private function queryBuilder(UserQueryModel $queryModel): QueryBuilder
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

        if ($queryModel->getExcludeIds()) {
            $query
                ->setParameter('excludeIds', $queryModel->getExcludeIds())
                ->andWhere(self::QUERY_ALIAS.'.id NOT IN (:excludeIds)')
            ;
        }

        return $query;
    }
}
