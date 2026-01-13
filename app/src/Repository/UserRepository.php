<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\EntityInterface;
use App\Contract\EntityQueryModelInterface;
use App\Contract\RepositoryInterface;
use App\Entity\User;
use App\Exception\Entity\EntityInvalidObjectTypeException;
use App\Exception\EntityQueryModel\EntityQueryModelInvalidObjectTypeException;
use App\Model\Query\UserQueryModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Ds\Vector;

/**
 * @extends ServiceEntityRepository<User>
 */
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

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    public function one(EntityQueryModelInterface $queryModel): ?User
    {
        if (!$queryModel instanceof UserQueryModel) {
            throw new EntityQueryModelInvalidObjectTypeException();
        }

        $queryBuilder = $this->queryBuilder(queryModel: $queryModel)->setMaxResults(maxResults: 1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    public function list(EntityQueryModelInterface $queryModel): Vector
    {
        if (!$queryModel instanceof UserQueryModel) {
            throw new EntityQueryModelInvalidObjectTypeException();
        }

        $queryBuilder = $this->queryBuilder(queryModel: $queryModel);

        return new Vector($queryBuilder->getQuery()->getResult());
    }

    /**
     * @throws EntityInvalidObjectTypeException
     */
    public function save(EntityInterface $entity): User
    {
        if (!$entity instanceof User) {
            throw new EntityInvalidObjectTypeException();
        }

        $this->em->persist(object: $entity);
        $this->em->flush();

        return $entity;
    }

    /**
     * @throws EntityInvalidObjectTypeException
     */
    public function delete(EntityInterface $entity): void
    {
        if (!$entity instanceof User) {
            throw new EntityInvalidObjectTypeException();
        }

        $this->em->remove(object: $entity);
        $this->em->flush();
    }

    private function queryBuilder(UserQueryModel $queryModel): QueryBuilder
    {
        $query = $this->createQueryBuilder(self::QUERY_ALIAS);

        foreach ($queryModel->getOrderBy() as $column => $order) {
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
