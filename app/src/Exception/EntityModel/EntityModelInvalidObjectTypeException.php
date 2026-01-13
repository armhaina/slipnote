<?php

declare(strict_types=1);

namespace App\Exception\EntityModel;

use App\Contract\ExceptionInterface;

class EntityModelInvalidObjectTypeException extends \Exception implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct(message: 'Неверный тип объекта');
    }
}
