<?php

declare(strict_types=1);

namespace App\Exception\EntityQueryModel;

use App\Contract\ExceptionInterface;

class EntityQueryModelInvalidObjectTypeException extends \Exception implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct(message: 'Неверный тип объекта');
    }
}
