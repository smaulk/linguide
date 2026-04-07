<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Dictionary\Enums\WordReviewSessionStatus;
use App\Core\Modules\Dictionary\Models\WordReviewSession;
use Illuminate\Support\Collection;

final class GetActiveWordReviewSessionTask extends Task
{
    /**
     * Возвращает активную сессию, если она не истекла.
     * Завершает все истекшие сессии.
     */
    public function run(int $userId): ?WordReviewSession
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
     * @return Collection<int, WordReviewSession>
     */
    private function getActiveSessions(int $userId): Collection
    {
        return WordReviewSession::query()
            ->select(['id', 'started_at'])
            ->where('user_id', $userId)
            ->where('status', WordReviewSessionStatus::ACTIVE)
            ->orderByDesc('started_at')
            ->get();
    }

    private function isExpiredSession(WordReviewSession $session): bool
    {
        return $session->started_at->lt(
            now()->subMinutes(WordReviewSession::SESSION_TIMEOUT_MINUTES)
        );
    }

    /**
     * @param int[] $ids
     */
    private function markSessionsAsAbandoned(array $ids): void
    {
        WordReviewSession::query()
            ->whereIn('id', $ids)
            ->update(['status' => WordReviewSessionStatus::ABANDONED]);
    }
}