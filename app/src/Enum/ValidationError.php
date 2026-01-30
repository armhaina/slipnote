<?php

declare(strict_types=1);

namespace App\Enum;

enum ValidationError: string
{
    case POSITIVE = 'Число должно быть положительным. Ваше число: {{ value }}';
    case RANGE = 'Значение {{ value }} должно быть в диапазоне от {{ min }} до {{ max }}';
    case RANGE_MIN = 'Минимально допустимое значение: {{ limit }}. Ваше число: {{ value }}';
    case RANGE_MAX = 'Максимально допустимое значение: {{ limit }}. Ваше число: {{ value }}';
    case LENGTH_MIN = 'Минимально допустимое значение символов: {{ limit }}. Ваше кол-во символов: {{ value_length }}';
    case LENGTH_MAX = 'Максимально допустимое значение символов: {{ limit }}. Ваше кол-во символов: {{ value_length }}';
    case TYPE_NUMERIC = 'Переданное значение должно содержать число. Вы передали: {{ value }}';

    case REQUIRED = 'Поле обязательно для заполнения';
    case INVALID_EMAIL = 'Неверный формат email';
    case TOO_SHORT = 'Минимальная длина - {{ limit }} символов';
    case TOO_LONG = 'Максимальная длина - {{ limit }} символов';
    case NOT_NUMERIC = 'Значение должно быть числом';
    case NOT_POSITIVE = 'Значение должно быть положительным числом';
    case INVALID_DATE = 'Неверный формат даты';
    case INVALID_ROLE = 'Недопустимая роль. Допустимые значения: {{ choices }}';
    case INVALID_UUID = 'Неверный формат UUID';
    case NOT_UNIQUE = 'Значение должно быть уникальным';
}
