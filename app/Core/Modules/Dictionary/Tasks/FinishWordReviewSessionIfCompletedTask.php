<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Dictionary\Enums\WordReviewSessionStatus;
use App\Core\Modules\Dictionary\Models\WordReviewSession;
use App\Core\Modules\Dictionary\Models\WordReviewSessionItem;

final class FinishWordReviewSessionIfCompletedTask extends Task
{
    public function run(int $sessionId): bool
    {
        $hasRemaining = WordReviewSessionItem::query()
            ->where('session_id', $sessionId)
            ->whereNull('answered_at')
            ->exists();

        if ($hasRemaining) {
            return false;
        }

        WordReviewSession::query()
            ->where('id', $sessionId)
            ->update([
                'status'      => WordReviewSessionStatus::FINISHED,
                'finished_at' => now(),
            ]);

        return true;
    }
}