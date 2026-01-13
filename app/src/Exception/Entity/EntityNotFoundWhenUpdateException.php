<?php

declare(strict_types=1);

namespace App\Exception\Entity;

use App\Contract\ExceptionInterface;

class EntityNotFoundWhenUpdateException extends \Exception implements ExceptionInterface
{
    public function __construct(string $entity)
    {
        parent::__construct("Entity {$entity} not found");
    }
}
