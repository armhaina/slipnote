<?php

declare(strict_types=1);

namespace App\Model\Payload;

use App\Enum\Message\ValidationError;
use Doctrine\DBAL\Types\Types;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UserCreatePayloadModel
{
    public function __construct(
        #[Assert\NotBlank(message: ValidationError::NOT_BLANK->value)]
        #[Assert\Type(type: Types::STRING)]
        #[Assert\Email(message: ValidationError::EMAIL->value)]
        #[OA\Property(description: 'Email')]
        private string $email,
        #[Assert\NotBlank(message: ValidationError::NOT_BLANK->value)]
        #[Assert\Type(type: Types::STRING)]
        #[Assert\Length(
            min: 6,
            max: 18,
            minMessage: ValidationError::LENGTH_MIN->value,
            maxMessage: ValidationError::LENGTH_MAX->value
        )]
        #[OA\Property(description: 'Пароль')]
        private string $password,
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
