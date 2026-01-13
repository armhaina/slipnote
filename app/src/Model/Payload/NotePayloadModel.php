<?php

declare(strict_types=1);

namespace App\Model\Payload;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

readonly class NotePayloadModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: Types::STRING)]
        private string $name,
        #[Assert\NotBlank]
        #[Assert\Type(type: Types::STRING)]
        private string $description,
        #[Assert\Type(type: Types::BOOLEAN)]
        private bool $isPrivate,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getIsPrivate(): bool
    {
        return $this->isPrivate;
    }
}
