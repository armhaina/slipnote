<?php

namespace App\Contract;

use App\Contract\Entity\EntityInterface;
use App\Contract\Entity\EntityQueryModelInterface;
use Ds\Sequence;

interface ServiceInterface
{
    public function get(int $id): EntityInterface;

    public function one(EntityQueryModelInterface $queryModel): ?EntityInterface;

    /**
     * @return Sequence<EntityInterface>
     */
    public function list(EntityQueryModelInterface $queryModel): Sequence;

    public function create(EntityInterface $entity): EntityInterface;

    public function update(EntityInterface $entity): EntityInterface;

    public function delete(EntityInterface $entity): void;

    public function count(EntityQueryModelInterface $queryModel): int;
}
