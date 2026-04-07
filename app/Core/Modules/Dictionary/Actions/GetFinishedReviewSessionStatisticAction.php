<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Dictionary\Dto\ReviewSessionStatisticDto;
use App\Core\Modules\Dictionary\Enums\WordReviewSessionStatus;
use App\Core\Modules\Dictionary\Models\WordReviewSession;
use App\Core\Modules\Dictionary\Models\WordReviewSessionItem;
use App\Core\Modules\Dictionary\Vo\Duration;
use Illuminate\Support\Collection;
use LogicException;

final class GetFinishedReviewSessionStatisticAction extends Action
{
    public function run(int $sessionId): ReviewSessionStatisticDto
    {
        $session = $this->getSession($sessionId);
        $words = $this->getSessionWords($session);
        $responseTimes = $this->getWordsResponseTimes($words);

        return new ReviewSessionStatisticDto(
            duration: $this->getSessionDuration($session),
            wordsCount: $words->count(),
            correctWordsCount: $words->sum(fn(WordReviewSessionItem $w) => $w->is_correct ? 1 : 0),
            avgResponseTime: Duration::fromSeconds((int)$responseTimes->avg()),
            maxResponseTime: Duration::fromSeconds((int)$responseTimes->max()),
            minResponseTime: Duration::fromSeconds((int)$responseTimes->min()),
        );
    }

    private function getSession(int $sessionId): WordReviewSession
    {
        return WordReviewSession::query()
            ->where('status', WordReviewSessionStatus::FINISHED)
            ->with([
                'items'
            ])
            ->findOrFail($sessionId);
    }

    private function getSessionDuration(WordReviewSession $session): Duration
    {
        if ($session->finished_at === null) {
            throw new LogicException('Сессия должна иметь дату завершения.');
        }

        return Duration::fromDates($session->started_at, $session->finished_at);
    }

    /**
     * @return Collection<int, WordReviewSessionItem>
     */
    private function getSessionWords(WordReviewSession $session): Collection
    {
        if ($session->items->isEmpty()) {
            throw new LogicException('Сессия должна содержать хотя бы 1 слово.');
        }

        return $session->items;
    }

    /**
     * @param Collection<int, WordReviewSessionItem> $words
     * @return Collection<int, int>
     */
    private function getWordsResponseTimes(Collection $words): Collection
    {
        return $words->map(function (WordReviewSessionItem $word) {
            if ($word->presented_at === null || $word->answered_at === null) {
                throw new LogicException('Все слова в сессии должны иметь дату показа и ответа');
            }

            return (int)$word->presented_at->diffInSeconds($word->answered_at, true);
        });
    }
}