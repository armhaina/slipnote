<?php

declare(strict_types=1);

namespace App\Model\Response\Exception;

use App\Contract\Exception\ExceptionResponseInterface;
use App\Enum\Group;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class ContextResponseModelException implements ExceptionResponseInterface
{
    public function __construct(
        #[Groups([Group::ADMIN->value])]
        #[OA\Property(
            description: 'Файл',
            type: 'string',
            example: '/pub/www/app/src/Controller/NoteController.php'
        )]
        private string $file,
        #[Groups([Group::ADMIN->value])]
        #[OA\Property(
            description: 'Строка',
            type: 'integer',
            example: 210
        )]
        private int $line,
        #[Groups([Group::ADMIN->value])]
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
            example: 'Ошибка'
        )]
        private string $message,
    ) {
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
