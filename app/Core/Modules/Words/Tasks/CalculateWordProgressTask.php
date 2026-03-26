<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Words\Enums\WordAnswerResult;
use App\Core\Modules\Words\Models\UserWordProgress;

final class CalculateWordProgressTask extends Task
{
    public function run(UserWordProgress $progress, WordAnswerResult $result): UserWordProgress
    {
        match ($result) {
            WordAnswerResult::CORRECT => $this->processCorrect($progress),
            WordAnswerResult::WRONG   => $this->processWrong($progress),
        };
        $this->processDateData($progress);

        return $progress;
    }

    private function processCorrect(UserWordProgress $progress): void
    {
        $progress->repetitions++;
        $progress->interval = match ($progress->repetitions) {
            1       => UserWordProgress::FIRST_INTERVAL,
            2       => UserWordProgress::SECOND_INTERVAL,
            default => $this->calculateInterval($progress),
        };
        $progress->ease_factor = $this->calculateCorrectEaseFactor($progress);
    }

    private function calculateInterval(UserWordProgress $progress): int
    {
        return min(
            (int)round($progress->interval * $progress->ease_factor),
            UserWordProgress::MAX_INTERVAL
        );
    }

    private function calculateCorrectEaseFactor(UserWordProgress $progress): float
    {
        return min(
            UserWordProgress::MAX_EASE_FACTOR,
            $progress->ease_factor + UserWordProgress::CORRECT_EASE_STEP
        );
    }

    private function processWrong(UserWordProgress $progress): void
    {
        $progress->repetitions = 0;
        $progress->interval = 1;
        $progress->ease_factor = $this->calculateWrongEaseFactor($progress);
    }

    private function calculateWrongEaseFactor(UserWordProgress $progress): float
    {
        return max(
            UserWordProgress::MIN_EASE_FACTOR,
            $progress->ease_factor - UserWordProgress::WRONG_EASE_STEP
        );
    }

    private function processDateData(UserWordProgress $progress): void
    {
        $now = now();
        $progress->due_at = $now->copy()->addDays($progress->interval);
        $progress->last_reviewed_at = $now;
    }
}