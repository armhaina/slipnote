<?php

declare(strict_types=1);

namespace App\Model\Query;

use App\Contract\EntityQueryModelInterface;

class UserQueryModel implements EntityQueryModelInterface
{
    private int $limit = 20;
    private int $offset = 0;
    private ?array $ids = null;
    private ?array $excludeIds = null;
    private ?array $roles = null;
    private array $orderBy = [];

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function getIds(): ?array
    {
        return $this->ids;
    }

    public function setIds(array $ids): self
    {
        $this->ids = $ids;

        return $this;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    public function setOrderBy(array $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function getExcludeIds(): ?array
    {
        return $this->excludeIds;
    }

    public function setExcludeIds(array $excludeIds): self
    {
        $this->excludeIds = $excludeIds;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
