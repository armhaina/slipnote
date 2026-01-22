<?php

declare(strict_types=1);

namespace App\Model\Payload;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

readonly class NotePayloadModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: Types::STRING)]
        #[Assert\Length(
            min: 2,
            max: 100,
            minMessage: 'Название должно содержать минимум {{ limit }} символа',
            maxMessage: 'Название должно содержать максимум {{ limit }} символов'
        )]
        private string $name,
        #[Assert\NotBlank]
        #[Assert\Type(type: Types::STRING)]
        #[Assert\Length(
            min: 2,
            max: 100,
            minMessage: 'Описание должно содержать минимум {{ limit }} символа',
            maxMessage: 'Описание должно содержать максимум {{ limit }} символов'
        )]
        private string $description,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
