<?php

declare(strict_types=1);

namespace App\Model\Response\Exception;

use App\Contract\ExceptionResponseInterface;
use App\Enum\Group;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class ValidationResponseModelException implements ExceptionResponseInterface
{
    /**
     * @param array<ViolationResponseModelException> $errors
     */
    public function __construct(
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Подтверждение',
            type: 'boolean',
            example: false
        )]
        private bool $success,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
            example: 'Ошибка валидации'
        )]
        private string $message,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Ошибки',
            type: 'array',
        )]
        private array $errors,
        #[Groups([Group::ADMIN->value])]
        #[OA\Property(
            description: 'Код ошибки (только администраторы)',
            type: 'integer',
            example: 0
        )]
        private int $code = 0
    ) {
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return array<ViolationResponseModelException>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
