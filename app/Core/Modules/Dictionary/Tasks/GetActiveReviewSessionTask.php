<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Dictionary\Enums\ReviewSessionStatus;
use App\Core\Modules\Dictionary\Models\ReviewSession;
use Illuminate\Support\Collection;

final class GetActiveReviewSessionTask extends Task
{
    /**
     * Возвращает активную сессию, если она не истекла.
     * Завершает все истекшие сессии.
     */
    public function run(int $userId): ?ReviewSession
    {
        $sessions = $this->getActiveSessions($userId);
        if ($sessions->isEmpty()) {
            return null;
        }

        $latest = $sessions->first();
        $isExpired = $this->isExpiredSession($latest);

        $idsToAbandon = $sessions
            ->slice($isExpired ? 0 : 1)
            ->pluck('id')
            ->all();

        if ($idsToAbandon !== []) {
            $this->markSessionsAsAbandoned($idsToAbandon);
        }

        return $isExpired ? null : $latest;
    }

    /**
     * @return Collection<int, ReviewSession>
     */
    private function getActiveSessions(int $userId): Collection
    {
        return ReviewSession::query()
            ->select(['id', 'started_at'])
            ->where('user_id', $userId)
            ->where('status', ReviewSessionStatus::ACTIVE)
            ->orderByDesc('started_at')
            ->get();
    }

    private function isExpiredSession(ReviewSession $session): bool
    {
        return $session->started_at->lt(
            now()->subMinutes(ReviewSession::SESSION_TIMEOUT_MINUTES)
        );
    }

    /**
     * @param int[] $ids
     */
    private function markSessionsAsAbandoned(array $ids): void
    {
        ReviewSession::query()
            ->whereIn('id', $ids)
            ->update(['status' => ReviewSessionStatus::ABANDONED]);
    }
}