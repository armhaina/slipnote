<?php

declare(strict_types=1);

namespace App\Model\Query;

use App\Contract\EntityQueryModelInterface;

class NoteQueryModel implements EntityQueryModelInterface
{
    private int $limit = 20;
    private int $offset = 0;
    private ?int $ownUserId = null;
    private ?array $ids = null;
    private ?array $userIds = null;
    private array $orderBy = [];
    private ?\DateTimeImmutable $updatedAtLess = null;

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

    public function getUserIds(): ?array
    {
        return $this->userIds;
    }

    public function setUserIds(array $userIds): self
    {
        $this->userIds = $userIds;

        return $this;
    }

    public function getOwnUserId(): ?int
    {
        return $this->ownUserId;
    }

    public function setOwnUserId(int $ownUserId): self
    {
        $this->ownUserId = $ownUserId;

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

    public function getUpdatedAtLess(): ?\DateTimeImmutable
    {
        return $this->updatedAtLess;
    }

    public function setUpdatedAtLess(\DateTimeImmutable $updatedAtLess): self
    {
        $this->updatedAtLess = $updatedAtLess;

        return $this;
    }
}
