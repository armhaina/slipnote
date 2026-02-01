<?php

declare(strict_types=1);

namespace App\Model\Query;

use App\Enum\Message\ValidationError;
use App\Validator\OrderBy;
use Nelmio\ApiDocBundle\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class NoteQueryModel
{
    #[Assert\Type(type: 'numeric')]
    #[Assert\Range(notInRangeMessage: ValidationError::RANGE->value, min: 1, max: 100)]
    private int $limit = 20;
    #[Assert\Type(type: 'numeric')]
    #[Assert\Range(minMessage: ValidationError::RANGE_MIN->value, min: 0)]
    private int $offset = 0;
    #[Assert\Type(type: 'boolean')]
    #[SerializedName(serializedName: 'is_trashed')]
    private ?bool $isTrashed = null;
    #[Assert\Type(type: 'string')]
    #[Assert\Length(
        max: 100,
        maxMessage: ValidationError::LENGTH_MAX->value
    )]
    private ?string $search = null;
    /** @var array<int> */
    #[Ignore]
    #[Assert\All([
        new Assert\Type(type: 'numeric', message: ValidationError::TYPE_NUMERIC->value),
        new Assert\Positive(message: ValidationError::POSITIVE->value),
    ])]
    private ?array $ids = null;
    /** @var array<int> */
    #[Ignore]
    #[SerializedName(serializedName: 'user_ids')]
    #[Assert\All([
        new Assert\Type(type: 'numeric', message: ValidationError::TYPE_NUMERIC->value),
        new Assert\Positive(message: ValidationError::POSITIVE->value),
    ])]
    private ?array $userIds = null;
    /** @var array<string> */
    #[Ignore]
    #[SerializedName(serializedName: 'order_by')]
    #[OrderBy(fields: ['name', 'created_at', 'updated_at'])]
    #[Assert\All([new Assert\Type(type: 'string')])]
    private array $orderBy = [];
    #[SerializedName(serializedName: 'updated_at_less')]
    #[Assert\DateTime(format: DATE_ATOM)]
    private ?string $updatedAtLess = null;
    #[SerializedName(serializedName: 'deleted_at_less')]
    #[Assert\DateTime(format: DATE_ATOM)]
    private ?string $deletedAtLess = null;

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

    /**
     * @throws \DateMalformedStringException
     */
    public function getUpdatedAtLess(): ?\DateTimeImmutable
    {
        if (is_string(value: $this->updatedAtLess)) {
            return new \DateTimeImmutable(datetime: $this->updatedAtLess);
        }

        return null;
    }

    public function setUpdatedAtLess(\DateTimeImmutable $updatedAtLess): self
    {
        $this->updatedAtLess = $updatedAtLess->format(format: DATE_ATOM);

        return $this;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function getDeletedAtLess(): ?\DateTimeImmutable
    {
        if (is_string(value: $this->deletedAtLess)) {
            return new \DateTimeImmutable(datetime: $this->deletedAtLess);
        }

        return null;
    }

    public function setDeletedAtLess(\DateTimeImmutable $deletedAtLess): self
    {
        $this->deletedAtLess = $deletedAtLess->format(format: DATE_ATOM);

        return $this;
    }
}
