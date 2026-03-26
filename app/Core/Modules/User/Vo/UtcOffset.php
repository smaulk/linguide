<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Vo;

use App\Core\Common\Parents\Vo;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

final readonly class UtcOffset extends Vo
{
    private const int MIN = -12;
    private const int MAX = 14;

    private function __construct(private int $value){}

    public static function fromInt(int $offset): self
    {
        if ($offset < self::MIN || $offset > self::MAX) {
            throw new InvalidArgumentException("Invalid UTC offset: $offset");
        }

        return new self($offset);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function format(): string
    {
        if ($this->value === 0) {
            return 'UTC';
        }

        return sprintf('UTC%+d', $this->value);
    }

    public function __toString(): string
    {
        return $this->format();
    }

    public function toTimezone(): string
    {
        return sprintf('%+03d:00', $this->value);
    }

    public function applyTo(Carbon $utc): Carbon
    {
        return $utc->copy()->setTimezone($this->toTimezone());
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * @return self[]
     */
    public static function all(): array
    {
        $result = [];
        for ($i = self::MIN; $i <= self::MAX; $i++) {
            $result[] = self::fromInt($i);
        }

        return $result;
    }
}