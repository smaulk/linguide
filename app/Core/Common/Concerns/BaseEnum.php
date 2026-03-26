<?php
declare(strict_types=1);

namespace App\Core\Common\Concerns;

use InvalidArgumentException;

trait BaseEnum
{
    /**
     * @return string[]|int[]
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * @return string[]
     */
    public static function names(): array
    {
        return array_map(fn(self $case) => $case->name, self::cases());
    }

    public static function fromName(string $name): static
    {
        foreach (static::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        throw new InvalidArgumentException("No enum case with name $name");
    }
}