<?php

namespace App\Support;

class ChangeFormatter
{
    public static function value(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '(vacío)';
        }

        if (is_bool($value)) {
            return $value ? 'Sí' : 'No';
        }

        if (is_array($value)) {
            return implode(', ', array_map(
                fn ($v) => self::value($v),
                $value
            ));
        }

        return (string) $value;
    }
}
