<?php

declare(strict_types=1);

namespace App\Model\Response\Action;

use OpenApi\Attributes as OA;

readonly class DeleteResponseModelAction
{
    public function __construct(
        #[OA\Property(
            description: 'Статус',
            type: 'boolean',
            default: true
        )]
        private bool $success = true,
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
            default: 'Запись успешно удалена'
        )]
        private string $message = 'Запись успешно удалена'
    ) {}

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
