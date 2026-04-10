<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Enums\ReviewAnswerResult;
use App\Core\Modules\Term\Models\LearningProgress;

final class CalculateLearningProgressTask extends Task
{
    public function run(LearningProgress $progress, ReviewAnswerResult $result): LearningProgress
    {
        match ($result) {
            ReviewAnswerResult::CORRECT => $this->processCorrect($progress),
            ReviewAnswerResult::WRONG   => $this->processWrong($progress),
        };
        $this->processDateData($progress);

        return $progress;
    }

    private function processCorrect(LearningProgress $progress): void
    {
        $progress->repetitions++;
        $progress->interval = match ($progress->repetitions) {
            1       => LearningProgress::FIRST_INTERVAL,
            2       => LearningProgress::SECOND_INTERVAL,
            default => $this->calculateInterval($progress),
        };
        $progress->ease_factor = $this->calculateCorrectEaseFactor($progress);
    }

    private function calculateInterval(LearningProgress $progress): int
    {
        return min(
            (int)round($progress->interval * $progress->ease_factor),
            LearningProgress::MAX_INTERVAL,
        );
    }

    private function calculateCorrectEaseFactor(LearningProgress $progress): float
    {
        return min(
            $progress->ease_factor + LearningProgress::CORRECT_EASE_STEP,
            LearningProgress::MAX_EASE_FACTOR,
        );
    }

    private function processWrong(LearningProgress $progress): void
    {
        $progress->repetitions = 0;
        $progress->interval = 0;
        $progress->ease_factor = $this->calculateWrongEaseFactor($progress);
    }

    private function calculateWrongEaseFactor(LearningProgress $progress): float
    {
        return max(
            $progress->ease_factor - LearningProgress::WRONG_EASE_STEP,
            LearningProgress::MIN_EASE_FACTOR,
        );
    }

    private function processDateData(LearningProgress $progress): void
    {
        $now = now();
        $progress->due_at = $now->copy()->addDays($progress->interval)->startOfDay();
        $progress->last_reviewed_at = $now;
    }
}