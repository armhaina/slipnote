<?php

declare(strict_types=1);

namespace App\Service\Entity;

use App\Contract\RepositoryInterface;

abstract readonly class AbstractService
{
    public function __construct(
        private RepositoryInterface $repository,
    ) {}

    public function transaction(callable $func): void
    {
        $this->repository->transaction(func: $func);
    }
}
