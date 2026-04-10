<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Enums\ReviewAnswerResult;
use App\Core\Modules\Term\Models\ReviewSessionItem;

final class SaveReviewSessionAnswerTask extends Task
{
    public function run(int $sessionId, int $termVariantId, ReviewAnswerResult $result): void
    {
        ReviewSessionItem::query()
            ->where('session_id', $sessionId)
            ->where('variant_id', $termVariantId)
            ->update([
                'is_correct'  => $this->isCorrectAnswer($result),
                'answered_at' => now(),
            ]);
    }

    private function isCorrectAnswer(ReviewAnswerResult $result): bool
    {
        return $result === ReviewAnswerResult::CORRECT;
    }
}