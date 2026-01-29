<?php

declare(strict_types=1);

namespace App\Model\Query;

use App\Validator\OrderBy;
use Nelmio\ApiDocBundle\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

class NoteQueryModel
{
    private int $limit = 20;
    private int $offset = 0;
    #[SerializedName(serializedName: 'is_trashed')]
    private ?bool $isTrashed = null;
    private ?string $search = null;
    /** @var array<int> */
    #[Ignore]
    private ?array $ids = null;
    /** @var array<int> */
    #[Ignore]
    #[SerializedName(serializedName: 'user_ids')]
    private ?array $userIds = null;
    /** @var array<string> */
    #[Ignore]
    #[SerializedName(serializedName: 'order_by')]
    #[OrderBy(fields: ['name', 'created_at', 'updated_at'])]
    private array $orderBy = [];
    #[SerializedName(serializedName: 'updated_at_less')]
    private ?\DateTimeImmutable $updatedAtLess = null;
    #[SerializedName(serializedName: 'deleted_at_less')]
    private ?\DateTimeImmutable $deletedAtLess = null;

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

    public function getIsTrashed(): ?bool
    {
        return $this->isTrashed;
    }

    public function setIsTrashed(bool $isTrashed): self
    {
        $this->isTrashed = $isTrashed;

        return $this;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(string $search): self
    {
        $this->search = $search;

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
     * @return null|array<int>
     */
    public function getUserIds(): ?array
    {
        return $this->userIds;
    }

    /**
     * @param array<int> $userIds
     */
    public function setUserIds(array $userIds): self
    {
        $this->userIds = $userIds;

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

    public function getUpdatedAtLess(): ?\DateTimeImmutable
    {
        return $this->updatedAtLess;
    }

    public function setUpdatedAtLess(\DateTimeImmutable $updatedAtLess): self
    {
        $this->updatedAtLess = $updatedAtLess;

        return $this;
    }

    public function getDeletedAtLess(): ?\DateTimeImmutable
    {
        return $this->deletedAtLess;
    }

    public function setDeletedAtLess(\DateTimeImmutable $deletedAtLess): self
    {
        $this->deletedAtLess = $deletedAtLess;

        return $this;
    }
}
