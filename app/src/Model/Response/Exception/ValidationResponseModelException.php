<?php

declare(strict_types=1);

namespace App\Model\Response\Exception;

use App\Contract\Exception\ExceptionResponseInterface;
use App\Enum\Group;
use Nelmio\ApiDocBundle\Attribute\Model;
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
            description: 'Статус',
            type: 'boolean',
        )]
        private bool $success,
        #[Groups([Group::PUBLIC->value, Group::ADMIN->value])]
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
        )]
        private string $message,
        #[Groups([Group::ADMIN->value])]
        #[OA\Property(
            description: 'Код ошибки (' . Group::ADMIN->value . ')',
            type: 'integer',
            default: null,
        )]
        private int $code,
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Ошибки',
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: ViolationResponseModelException::class,
                )
            )
        )]
        private array $errors,
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
