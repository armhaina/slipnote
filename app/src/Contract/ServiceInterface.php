<?php

namespace App\Contract;

use Ds\Sequence;
use Ds\Vector;

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
