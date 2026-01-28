<?php

declare(strict_types=1);

namespace App\Model\Payload;

use Doctrine\DBAL\Types\Types;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UserUpdatePasswordPayloadModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: Types::STRING)]
        #[OA\Property(description: 'Текущий пароль')]
        #[SerializedName(serializedName: 'current_password')]
        private string $currentPassword,
        #[Assert\NotBlank]
        #[Assert\Type(type: Types::STRING)]
        #[Assert\Length(
            min: 6,
            max: 18,
            minMessage: 'Пароль должен содержать минимум {{ limit }} символов',
            maxMessage: 'Пароль должен содержать максимум {{ limit }} символов'
        )]
        #[OA\Property(description: 'Новый пароль')]
        #[SerializedName(serializedName: 'new_password')]
        private string $newPassword,
    ) {}

    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}
