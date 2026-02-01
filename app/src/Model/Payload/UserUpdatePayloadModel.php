<?php

declare(strict_types=1);

namespace App\Model\Payload;

use App\Enum\ValidationError;
use Doctrine\DBAL\Types\Types;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\DisableAutoMapping]
readonly class UserUpdatePayloadModel
{
    public function __construct(
        #[Assert\NotBlank(message: ValidationError::NOT_BLANK->value)]
        #[Assert\Type(type: Types::STRING)]
        #[Assert\Email(message: ValidationError::EMAIL->value)]
        #[OA\Property(description: 'Email')]
        private string $email,
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }
}
