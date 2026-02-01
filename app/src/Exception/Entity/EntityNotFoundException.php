<?php

declare(strict_types=1);

namespace App\Exception\Entity;

use App\Contract\ExceptionInterface;

class EntityNotFoundException extends \Exception implements ExceptionInterface
{
    public function __construct(string $entity, ?int $id = null)
    {
        if ($id) {
            $message = 'Сущность '.$entity.' с id '.$id.' не найдена';
        } else {
            $message = 'Сущность '.$entity.' не найдена';
        }

        parent::__construct(message: $message);
    }
}
