<?php

declare(strict_types=1);

namespace App\Model\Response\Exception;

use App\Contract\Exception\ExceptionResponseInterface;
use App\Enum\Group;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class DefaultResponseModelException implements ExceptionResponseInterface
{
    public function __construct(
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Статус',
            type: 'boolean',
        )]
        private bool $success,
        #[Groups([Group::PUBLIC->value])]
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
        )]
        private string $message,
        #[Groups([Group::ADMIN->value])]
        #[OA\Property(
            ref: new Model(
                type: ContextResponseModelException::class,
            ),
            description: 'Контекст (' . Group::ADMIN->value . ')',
            type: 'object',
            nullable: true
        )]
        private ContextResponseModelException $context,
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

    public function getContext(): ContextResponseModelException
    {
        return $this->context;
    }
}
