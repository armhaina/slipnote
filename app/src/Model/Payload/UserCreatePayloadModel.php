<?php

declare(strict_types=1);

namespace App\Model\Payload;

use Doctrine\DBAL\Types\Types;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UserCreatePayloadModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: Types::STRING)]
        #[Assert\Email(message: 'Email не соответствует формату электронной почты')]
        #[OA\Property(description: 'Email')]
        private string $email,
        #[Assert\NotBlank]
        #[Assert\Type(type: Types::STRING)]
        #[Assert\Length(
            min: 6,
            max: 18,
            minMessage: 'Пароль должен содержать минимум {{ limit }} символов',
            maxMessage: 'Пароль должен содержать максимум {{ limit }} символов'
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
