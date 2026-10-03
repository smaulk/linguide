<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Enums\ReviewSessionStatus;
use App\Core\Modules\User\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

final class GetUsersForReviewReminderTask extends Task
{
    /**
     * Возвращает пользователей, у которых локальное время наступило после
     * указанного часа и которые ещё не завершали сессию в текущий локальный день.
     *
     * @return list<int>
     */
    public function run(int $startHour): array
    {
        if ($startHour < 0 || $startHour > 23) {
            throw new InvalidArgumentException('Start hour must be between 0 and 23.');
        }

        $now = now('UTC');

        /** @var list<int> */
        return User::query()
            ->join('user_settings', 'user_settings.user_id', '=', 'users.id')
            ->where('user_settings.reminders_enabled', true)
            ->whereNotNull('user_settings.utc_offset')
            ->whereRaw(
                'MOD(? + user_settings.utc_offset + 24, 24) >= ?',
                [$now->hour, $startHour],
            )
            ->whereNotExists(
                fn (Builder $query) => $this->addFinishedSessionCondition($query, $now),
            )
            ->pluck('users.id')
            ->all();
    }

    /**
     * Проверка, что у пользователя есть завершенная сессия в текущий локальный день.
     */
    private function addFinishedSessionCondition(
        Builder $query,
        CarbonInterface $now,
    ): void {
        // Вычисляем начало текущего локального дня пользователя в UTC с учётом его UTC offset.
        $localDayStart = <<<SQL
            date_trunc(
                'day',
                ?::timestamptz + make_interval(hours => user_settings.utc_offset)
            ) - make_interval(hours => user_settings.utc_offset)
        SQL;

        $query
            ->selectRaw('1')
            ->from('review_sessions')
            ->whereColumn('review_sessions.user_id', 'users.id')
            ->where('review_sessions.status', ReviewSessionStatus::FINISHED)
            ->whereRaw(
                "review_sessions.finished_at >= ({$localDayStart})",
                [$now],
            )
            ->whereRaw(
                "review_sessions.finished_at < ({$localDayStart} + interval '1 day')",
                [$now],
            );
    }
}