<?php

declare(strict_types=1);

namespace App\Model\Query;

use App\Contract\Entity\EntityQueryModelInterface;
use Nelmio\ApiDocBundle\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;
use OpenApi\Attributes as OA;

class NoteQueryModel implements EntityQueryModelInterface
{
    private int $limit = 20;
    private int $offset = 0;
    /** @var array<int> */
    private ?array $ids = null;
    /** @var array<int> */
    #[SerializedName(serializedName: 'user_ids')]
    private ?array $userIds = null;
    /** @var array<string> */
    #[Ignore]
    #[SerializedName(serializedName: 'order_by')]
    private array $orderBy = [];
    #[SerializedName(serializedName: 'updated_at_less')]
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

    /**
     * @return array<int>|null
     */
    public function getIds(): ?array
    {
        return $this->ids;
    }

    /**
     * @param array<int> $ids
     * @return NoteQueryModel
     */
    public function setIds(array $ids): self
    {
        $this->ids = $ids;

        return $this;
    }

    /**
     * @return array<int>|null
     */
    public function getUserIds(): ?array
    {
        return $this->userIds;
    }

    /**
     * @param array<int> $userIds
     * @return NoteQueryModel
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
     * @return NoteQueryModel
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
}
