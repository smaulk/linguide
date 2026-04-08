<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Dictionary\Enums\ReviewSessionStatus;
use App\Core\Modules\Dictionary\Models\ReviewSession;
use App\Core\Modules\Dictionary\Models\ReviewSessionItem;

final class FinishReviewSessionIfCompletedTask extends Task
{
    public function run(int $sessionId): bool
    {
        $hasRemaining = ReviewSessionItem::query()
            ->where('session_id', $sessionId)
            ->whereNull('answered_at')
            ->exists();

        if ($hasRemaining) {
            return false;
        }

        ReviewSession::query()
            ->where('id', $sessionId)
            ->update([
                'status'      => ReviewSessionStatus::FINISHED,
                'finished_at' => now(),
            ]);

        return true;
    }
}