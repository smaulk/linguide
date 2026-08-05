<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Enums\ReviewMode;
use App\Core\Modules\Term\Models\LearningProgress;
use App\Core\Modules\Term\Models\ReviewSessionItem;
use Illuminate\Support\Collection;

final class CreateReviewSessionItemsTask extends Task
{
    /**
     * @param Collection<int, LearningProgress> $learningTerms
     */
    public function run(int $sessionId, Collection $learningTerms): void
    {
        if ($learningTerms->isEmpty()) {
            return;
        }

        ReviewSessionItem::query()->insert(
            $this->prepareRows($sessionId, $learningTerms)
        );
    }

    /**
     * @param Collection<int, LearningProgress> $learningTerms
     * @return array<int, array<string, mixed>>
     */
    private function prepareRows(int $sessionId, Collection $learningTerms): array
    {
        return $learningTerms->map(fn(LearningProgress $term) => [
            'session_id' => $sessionId,
            'variant_id' => $term->variant_id,
            'mode'       => $this->getReviewMode($term)->value,
        ])->all();
    }

    /**
     * Определяет режим повторения слова на основе текущего прогресса его изучения.
     */
    private function getReviewMode(LearningProgress $term): ReviewMode
    {
        return rand(1, 100) <= $term->reverse_chance
            ? ReviewMode::RussianToEnglish
            : ReviewMode::EnglishToRussian;
    }
}