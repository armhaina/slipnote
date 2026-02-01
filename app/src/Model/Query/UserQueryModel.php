<?php

declare(strict_types=1);

namespace App\Model\Query;

use Symfony\Component\Validator\Constraints as Assert;

class UserQueryModel
{
    #[Assert\Positive]
    #[Assert\Range(min: 0, max: 100)]
    private int $limit = 20;
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0)]
    private int $offset = 0;
    #[Assert\Type(type: 'string')]
    #[Assert\Email]
    private ?string $email = null;
    /** @var array<int> */
    #[Assert\All([new Assert\Type(type: 'numeric'), new Assert\Positive()])]
    private ?array $ids = null;
    /** @var array<int> */
    #[Assert\All([new Assert\Type(type: 'numeric'), new Assert\Positive()])]
    private ?array $excludeIds = null;
    /** @var array<string> */
    #[Assert\All([new Assert\Type(type: 'string')])]
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
}
