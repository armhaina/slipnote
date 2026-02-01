<?php

declare(strict_types=1);

namespace App\Enum\Message;

enum ValidationViolationMessage: string
{
    case POSITIVE = 'Число должно быть положительным. Ваше число: {{ value }}';
    case RANGE = 'Значение {{ value }} должно быть в диапазоне от {{ min }} до {{ max }}';
    case RANGE_MIN = 'Минимально допустимое число: {{ limit }}. Ваше число: {{ value }}';
    case RANGE_MAX = 'Максимально допустимое число: {{ limit }}. Ваше число: {{ value }}';
    case LENGTH_MIN = 'Минимально допустимое кол-во символов: {{ limit }}. Ваше кол-во символов: {{ value_length }}';
    case LENGTH_MAX = 'Максимально допустимое кол-во символов: {{ limit }}. Ваше кол-во символов: {{ value_length }}';
    case TYPE_NUMERIC = 'Переданное значение должно содержать число. Вы передали: {{ value }}';
    case NOT_BLANK = 'Поле не может быть пустым';
    case EMAIL = 'Email не соответствует формату электронной почты';
    case CUSTOM_ORDER_BY_ALLOWED_FIELDS = 'Поле {{ field }} не разрешено для сортировки. Разрешены поля: {{ allowed }}';
    case CUSTOM_ORDER_BY_ALLOWED_DIRECTIONS = 'Направление {{ direction }} недопустимо. Используйте: asc или desc';
}
