<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Vo;

use App\Core\Common\Parents\Vo;
use Illuminate\Support\Carbon;

final readonly class Duration extends Vo
{
    private function __construct(private int $seconds){}

    public static function fromSeconds(int $seconds): self
    {
        return new self(abs($seconds));
    }

    public static function fromDates(Carbon $start, Carbon $end): self
    {
        return self::fromSeconds(
            (int)$start->diffInSeconds($end, true)
        );
    }

    public function seconds(): int
    {
        return $this->seconds;
    }

    public function format(): string
    {
        $hours = intdiv($this->seconds, 3600);
        $minutes = intdiv($this->seconds % 3600, 60);
        $seconds = $this->seconds % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = "{$hours} ч";
        }
        if ($minutes > 0) {
            $parts[] = "{$minutes} мин";
        }
        if ($seconds > 0 || empty($parts)) {
            $parts[] = "{$seconds} сек";
        }

        return implode(' ', $parts);
    }

    public function __toString(): string
    {
        return $this->format();
    }
}