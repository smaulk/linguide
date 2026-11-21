<?php

declare(strict_types=1);

namespace App\Core\Modules\Term\Enums;

use Illuminate\Support\Carbon;

enum ReviewStatisticsPeriod: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case HALF_YEAR = 'half_year';
    case YEAR = 'year';
    case ALL_TIME = 'all_time';

    public function label(): string
    {
        return match ($this) {
            self::DAY => '📅 День',
            self::WEEK => '🗓 Неделя',
            self::MONTH => '📆 Месяц',
            self::HALF_YEAR => '📊 Полгода',
            self::YEAR => '📈 Год',
            self::ALL_TIME => '♾️ За всё время',
        };
    }

    public static function fromLabel(string $label): ?self
    {
        foreach (self::cases() as $period) {
            if ($period->label() === $label) {
                return $period;
            }
        }

        return null;
    }

    public function startAt(Carbon $end): ?Carbon
    {
        return match ($this) {
            self::DAY => $end->copy()->subDay(),
            self::WEEK => $end->copy()->subDays(7),
            self::MONTH => $end->copy()->subDays(30),
            self::HALF_YEAR => $end->copy()->subMonthsNoOverflow(6),
            self::YEAR => $end->copy()->subYearNoOverflow(),
            self::ALL_TIME => null,
        };
    }

    public function periodName(): string
    {
        return match ($this) {
            self::DAY => 'за последние 24 часа',
            self::WEEK => 'за последние 7 дней',
            self::MONTH => 'за последние 30 дней',
            self::HALF_YEAR => 'за последние 6 месяцев',
            self::YEAR => 'за последние 12 месяцев',
            self::ALL_TIME => 'за всё время',
        };
    }
}
