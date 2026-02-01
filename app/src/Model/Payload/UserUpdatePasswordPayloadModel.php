<?php

declare(strict_types=1);

namespace App\Model\Payload;

use App\Enum\Message\ValidationViolationMessage;
use Doctrine\DBAL\Types\Types;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UserUpdatePasswordPayloadModel
{
    public function __construct(
        #[Assert\NotBlank(message: ValidationViolationMessage::NOT_BLANK->value)]
        #[Assert\Type(type: Types::STRING)]
        #[OA\Property(description: 'Текущий пароль')]
        #[SerializedName(serializedName: 'current_password')]
        private string $currentPassword,
        #[Assert\NotBlank(message: ValidationViolationMessage::NOT_BLANK->value)]
        #[Assert\Type(type: Types::STRING)]
        #[Assert\Length(
            min: 6,
            max: 18,
            minMessage: ValidationViolationMessage::LENGTH_MIN->value,
            maxMessage: ValidationViolationMessage::LENGTH_MAX->value
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
