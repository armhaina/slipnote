<?php

namespace App\Contract;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Ds\Vector;

interface RepositoryInterface
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em);

    public function transaction(callable $func): void;

    public function get(int $id): ?EntityInterface;

    public function one(EntityQueryModelInterface $queryModel): ?EntityInterface;

    public function list(EntityQueryModelInterface $queryModel): Vector;

    public function save(EntityInterface $entity): EntityInterface;

    public function delete(EntityInterface $entity): void;
}
