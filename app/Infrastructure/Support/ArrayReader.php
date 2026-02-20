<?php
declare(strict_types=1);

namespace App\Infrastructure\Support;

use InvalidArgumentException;

class ArrayReader
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(protected array $data){}

    /**
     * @throws InvalidArgumentException
     */
    public function string(string $key, ?string $default = null): string
    {
        $value = $this->data[$key] ?? null;

        if ($value === null) {
            if ($default !== null) {
                return $default;
            }
            throw new InvalidArgumentException("Key '{$key}' is required");
        }

        if (!is_string($value) || $value === '') {
            throw new InvalidArgumentException("Key '{$key}' must be non-empty string");
        }

        return $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function nullableString(string $key): ?string
    {
        $value = $this->data[$key] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException("Key '{$key}' must be string or empty");
        }

        return $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function int(string $key, ?int $default = null): int
    {
        $value = $this->data[$key] ?? null;

        if ($value === null) {
            if ($default !== null) {
                return $default;
            }
            throw new InvalidArgumentException("Key '{$key}' is required");
        }

        if (!is_int($value) && !ctype_digit((string)$value)) {
            throw new InvalidArgumentException("Key '{$key}' must be integer");
        }

        return (int)$value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function nullableInt(string $key): ?int
    {
        $value = $this->data[$key] ?? null;

        if ($value === null) {
            return null;
        }

        if (!is_int($value) && !ctype_digit((string)$value)) {
            throw new InvalidArgumentException("Key '{$key}' must be integer or empty");
        }

        return (int)$value;

    }
}