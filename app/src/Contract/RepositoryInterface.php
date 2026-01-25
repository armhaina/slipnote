<?php

namespace App\Contract;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

interface RepositoryInterface
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em);

    public function transaction(callable $func): void;
}
