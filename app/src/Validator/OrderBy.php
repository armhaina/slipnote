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
    public array $allowedFields;
    /**
     * @var array<string>
     */
    public array $allowedDirections = ['asc', 'desc'];

    /**
     * @param string[] $fields
     */
    #[HasNamedArguments]
    public function __construct(array $fields)
    {
        $this->allowedFields = $fields;

        parent::__construct();
    }
}
