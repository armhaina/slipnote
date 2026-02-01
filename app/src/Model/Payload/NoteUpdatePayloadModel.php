<?php

declare(strict_types=1);

namespace App\Model\Payload;

use App\Enum\Message\ValidationError;
use Doctrine\DBAL\Types\Types;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class NoteUpdatePayloadModel
{
    public function __construct(
        #[Assert\NotBlank(message: ValidationError::NOT_BLANK->value)]
        #[Assert\Type(type: Types::STRING)]
        #[Assert\Length(
            min: 1,
            max: 100,
            minMessage: ValidationError::LENGTH_MIN->value,
            maxMessage: ValidationError::LENGTH_MAX->value
        )]
        #[OA\Property(description: 'Наименование')]
        private string $name,
        #[Assert\Type(type: Types::STRING)]
        #[Assert\Length(
            min: 0,
            max: 10000,
            minMessage: ValidationError::LENGTH_MIN->value,
            maxMessage: ValidationError::LENGTH_MAX->value
        )]
        #[OA\Property(description: 'Описание')]
        private ?string $description = null,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
