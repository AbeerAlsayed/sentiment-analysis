<?php

namespace App\Enum;

enum SentimentEnum: string
{
    case Positive = 'positive';
    case Negative = 'negative';

    public static function getValues(): array
    {
        return array_map(fn($enum) => $enum->value, self::cases());
    }
}
