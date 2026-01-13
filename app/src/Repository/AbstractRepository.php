<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __construct(
        string $entityClass,
        ManagerRegistry $registry,
        protected readonly EntityManagerInterface $em,
    ) {
        parent::__construct(registry: $registry, entityClass: $entityClass);
    }

    public function transaction(callable $func): void
    {
        $this->em->wrapInTransaction(func: $func);
    }
}
