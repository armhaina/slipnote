<?php

declare(strict_types=1);

namespace App\Exception\Entity;

use App\Contract\Exception\ExceptionInterface;

class EntityInvalidObjectTypeException extends \Exception implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct(message: 'Неверный тип объекта');
    }
}
