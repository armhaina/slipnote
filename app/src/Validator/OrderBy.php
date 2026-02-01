<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class OrderBy extends Constraint
{
    /**
     * @var array<string>
     */
    public array $allowedDirections = ['asc', 'desc'];

    /**
     * @param string[] $allowedFields
     */
    #[HasNamedArguments]
    public function __construct(public array $allowedFields)
    {
        parent::__construct();
    }
}
