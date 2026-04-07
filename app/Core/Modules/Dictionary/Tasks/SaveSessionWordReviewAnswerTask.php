<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Dictionary\Enums\WordReviewAnswerResult;
use App\Core\Modules\Dictionary\Models\WordReviewSessionItem;

final class SaveSessionWordReviewAnswerTask extends Task
{
    public function run(int $sessionId, int $wordId, WordReviewAnswerResult $result): void
    {
        WordReviewSessionItem::query()
            ->where('session_id', $sessionId)
            ->where('word_id', $wordId)
            ->update([
                'is_correct' => $this->isCorrectAnswer($result),
                'answered_at' => now(),
            ]);
    }

    private function isCorrectAnswer(WordReviewAnswerResult $result): bool
    {
        return $result === WordReviewAnswerResult::CORRECT;
    }
}