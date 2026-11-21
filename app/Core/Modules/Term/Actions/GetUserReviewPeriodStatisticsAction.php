<?php

declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Term\Dto\ReviewPeriodStatisticsDto;
use App\Core\Modules\Term\Enums\ReviewSessionStatus;
use App\Core\Modules\Term\Enums\ReviewStatisticsPeriod;
use App\Core\Modules\Term\Models\LearningProgress;
use App\Core\Modules\Term\Models\ReviewSession;
use App\Core\Modules\Term\Models\ReviewSessionItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class GetUserReviewPeriodStatisticsAction extends Action
{
    public function run(int $userId, ReviewStatisticsPeriod $period, ?int $utcOffset = null): ReviewPeriodStatisticsDto
    {
        $timezone = $this->timezone($utcOffset);
        $end = now()->utc();
        $start = $period->startAt($end);

        $finishedSessions = $this->finishedSessionsQuery($userId, $start, $end);

        $sessionsCount = (clone $finishedSessions)->count();

        $answeredItems = ReviewSessionItem::query()
            ->whereIn('session_id', (clone $finishedSessions)->select('id'))
            ->whereNotNull('answered_at');

        $answeredTermsCount = (clone $answeredItems)->count();
        $correctAnswersCount = (clone $answeredItems)
            ->where('review_session_items.is_correct', true)
            ->count();

        $activityTimestamps = (clone $finishedSessions)
            ->orderBy('finished_at')
            ->pluck('finished_at');
        $activityDays = $this->getActivityDays($activityTimestamps, $timezone);

        $streakSessions = $period === ReviewStatisticsPeriod::ALL_TIME
            ? $finishedSessions
            : $this->finishedSessionsQuery($userId, null, $end);
        $streakTimestamps = (clone $streakSessions)
            ->orderBy('finished_at')
            ->pluck('finished_at');
        $allTimeActivityDays = $period === ReviewStatisticsPeriod::ALL_TIME
            ? $activityDays
            : $this->getActivityDays($streakTimestamps, $timezone);

        return new ReviewPeriodStatisticsDto(
            sessionsCount: $sessionsCount,
            answeredTermsCount: $answeredTermsCount,
            correctAnswersPercent: $answeredTermsCount === 0
                ? 0.0
                : round($correctAnswersCount / $answeredTermsCount * 100, 1),
            wellLearnedWordsCount: $this->getWellLearnedWordsCount($userId),
            activeDaysCount: $activityDays->count(),
            longestActivityStreak: $this->getLongestActivityStreak($allTimeActivityDays),
        );
    }

    /** @return Builder<ReviewSession> */
    private function finishedSessionsQuery(int $userId, ?Carbon $start, Carbon $end): Builder
    {
        $query = ReviewSession::query()
            ->where('user_id', $userId)
            ->where('status', ReviewSessionStatus::FINISHED)
            ->where('finished_at', '<=', $end);

        if ($start !== null) {
            $query->where('finished_at', '>=', $start);
        }

        return $query;
    }

    private function getWellLearnedWordsCount(int $userId): int
    {
        return LearningProgress::query()
            ->join('term_variants', 'term_variants.id', '=', 'learning_progress.variant_id')
            ->where('learning_progress.user_id', $userId)
            ->where('learning_progress.repetitions', '>=', 5)
            ->distinct()
            ->count('term_variants.term_id');
    }

    /** @param Collection<int, Carbon> $days */
    private function getLongestActivityStreak(Collection $days): int
    {
        $days = $days->values();
        if ($days->isEmpty()) {
            return 0;
        }

        $longest = 1;
        $current = 1;
        for ($i = 1; $i < $days->count(); $i++) {
            $previousDay = $days->get($i - 1);
            $currentDay = $days->get($i);
            if ($previousDay instanceof Carbon && $currentDay instanceof Carbon
                && $previousDay->copy()->addDay()->isSameDay($currentDay)) {
                $current++;
                $longest = max($longest, $current);

                continue;
            }

            $current = 1;
        }

        return $longest;
    }

    /** @param iterable<string> $timestamps
     * @return Collection<int, Carbon>
     */
    private function getActivityDays(iterable $timestamps, string $timezone): Collection
    {
        /** @var Collection<int, Carbon> $days */
        $days = collect();
        foreach ($timestamps as $timestamp) {
            $days->push(Carbon::parse($timestamp, 'UTC')->setTimezone($timezone)->startOfDay());
        }

        return $days
            ->unique(fn (Carbon $day) => $day->toDateString())
            ->sortBy(fn (Carbon $day) => $day->getTimestamp())
            ->values();
    }

    private function timezone(?int $utcOffset): string
    {
        return $utcOffset === null
            ? (string) config('app.timezone', 'UTC')
            : sprintf('%+03d:00', $utcOffset);
    }
}
