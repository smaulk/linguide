<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Classes;

final class CallbackParser
{
    public static function isMatch(string $callback, string $prefix): bool
    {
        // prefix без параметра
        if (!str_ends_with($prefix, ':')) {
            return $callback === $prefix;
        }

        $prefixLen = strlen($prefix);

        // должен начинаться с prefix
        if (strncmp($callback, $prefix, $prefixLen) !== 0) {
            return false;
        }

        // после prefix не должно быть еще ':'
        return strpos($callback, ':', $prefixLen) === false;
    }

    public static function parseIntValue(string $callback, string $prefix): ?int
    {
        if (!self::isMatch($callback, $prefix)) {
            return null;
        }

        $value = substr($callback, strlen($prefix));

        // строгая проверка, что это именно int
        if ($value === '' || !ctype_digit($value)) {
            return null;
        }

        return (int)$value;
    }
}