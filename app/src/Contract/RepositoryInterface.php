<?php

namespace App\Contract;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

interface RepositoryInterface
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, PaginatorInterface $paginator);

    public function transaction(callable $func): void;
}
