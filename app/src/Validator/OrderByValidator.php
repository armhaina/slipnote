<?php

namespace App\Validator;

use App\Enum\Message\ValidationError;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OrderByValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof OrderBy) {
            return;
        }

        if (!is_array($value)) {
            return;
        }

        // Проверяем ключи (поля)
        foreach (array_keys($value) as $field) {
            if (!in_array($field, $constraint->allowedFields, true)) {
                $this->context->buildViolation(ValidationError::CUSTOM_ORDER_BY_ALLOWED_FIELDS->value)
                    ->setParameter('{{ field }}', $field)
                    ->setParameter('{{ allowed }}', implode(', ', $constraint->allowedFields))
                    ->addViolation()
                ;
            }
        }

        // Проверяем значения (направления)
        foreach ($value as $direction) {
            if (!in_array($direction, $constraint->allowedDirections, true)) {
                $this->context->buildViolation(ValidationError::CUSTOM_ORDER_BY_ALLOWED_DIRECTIONS->value)
                    ->setParameter('{{ direction }}', $direction)
                    ->addViolation()
                ;
            }
        }
    }
}
