<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Model\Query\UserQueryModel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Ds\Vector;
use Knp\Component\Pager\PaginatorInterface;

class UserRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ) {
        parent::__construct(entityClass: User::class, registry: $registry, em: $em, paginator: $paginator);
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
        $query = $this->createQueryBuilder(User::shortName());

        $query->setFirstResult($queryModel->getOffset());
        $query->setMaxResults($queryModel->getLimit());

        foreach ($queryModel->getOrderBy() as $column => $order) {
            $column = $this->convertSnakeCaseToCamelCase(value: $column);
            $query->addOrderBy(sort: User::shortName().'.'.$column, order: $order);
        }

        if ($queryModel->getIds()) {
            $query
                ->setParameter('ids', $queryModel->getIds())
                ->andWhere(User::shortName().'.id IN (:ids)')
            ;
        }

        if ($queryModel->getExcludeIds()) {
            $query
                ->setParameter('excludeIds', $queryModel->getExcludeIds())
                ->andWhere(User::shortName().'.id NOT IN (:excludeIds)')
            ;
        }

        if ($queryModel->getEmail()) {
            $query
                ->setParameter('email', $queryModel->getEmail())
                ->andWhere(User::shortName().'.email = :email')
            ;
        }

        return $query;
    }
}
