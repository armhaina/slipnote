<?php

declare(strict_types=1);

namespace App\Model\Response\Exception;

use App\Contract\Exception\ExceptionResponseInterface;
use App\Enum\Entity\User\GroupUser;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class ExpiredJWTTokenModelException implements ExceptionResponseInterface
{
    public function __construct(
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Код ошибки',
            type: 'integer',
            nullable: true
        )]
        private int $code = 401,
        #[Groups([GroupUser::PUBLIC->value])]
        #[OA\Property(
            description: 'Сообщение',
            type: 'string',
        )]
        private string $message = 'Expired JWT Token',
    ) {}

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
