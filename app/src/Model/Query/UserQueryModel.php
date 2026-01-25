<?php

declare(strict_types=1);

namespace App\Model\Query;

use App\Contract\Entity\EntityQueryModelInterface;

class UserQueryModel implements EntityQueryModelInterface
{
    private int $limit = 20;
    private int $offset = 0;
    /** @var array<int> */
    private ?array $ids = null;
    /** @var array<int> */
    private ?array $excludeIds = null;
    /** @var array<string> */
    private ?array $roles = null;
    /** @var array<string> */
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

    /**
     * @return null|array<int>
     */
    public function getIds(): ?array
    {
        return $this->ids;
    }

    /**
     * @param array<int> $ids
     */
    public function setIds(array $ids): self
    {
        $this->ids = $ids;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @param array<string> $orderBy
     */
    public function setOrderBy(array $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return null|array<int>
     */
    public function getExcludeIds(): ?array
    {
        return $this->excludeIds;
    }

    /**
     * @param array<int> $excludeIds
     */
    public function setExcludeIds(array $excludeIds): self
    {
        $this->excludeIds = $excludeIds;

        return $this;
    }

    /**
     * @return null|array<string>
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
