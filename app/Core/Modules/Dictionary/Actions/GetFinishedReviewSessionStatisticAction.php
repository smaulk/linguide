<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Dictionary\Dto\ReviewSessionStatisticDto;
use App\Core\Modules\Dictionary\Enums\ReviewSessionStatus;
use App\Core\Modules\Dictionary\Models\ReviewSession;
use App\Core\Modules\Dictionary\Models\ReviewSessionItem;
use App\Core\Modules\Dictionary\Vo\Duration;
use Illuminate\Support\Collection;
use LogicException;

final class GetFinishedReviewSessionStatisticAction extends Action
{
    public function run(int $sessionId): ReviewSessionStatisticDto
    {
        $session = $this->getSession($sessionId);
        $items = $this->getSessionItems($session);
        $responseTimes = $this->getResponseTimes($items);

        return new ReviewSessionStatisticDto(
            duration: $this->getSessionDuration($session),
            termsCount: $items->count(),
            correctTermsCount: $items->sum(fn(ReviewSessionItem $i) => $i->is_correct ? 1 : 0),
            avgResponseTime: Duration::fromSeconds((int)$responseTimes->avg()),
            maxResponseTime: Duration::fromSeconds((int)$responseTimes->max()),
            minResponseTime: Duration::fromSeconds((int)$responseTimes->min()),
        );
    }

    private function getSession(int $sessionId): ReviewSession
    {
        return ReviewSession::query()
            ->where('status', ReviewSessionStatus::FINISHED)
            ->with(['items'])
            ->findOrFail($sessionId);
    }

    private function getSessionDuration(ReviewSession $session): Duration
    {
        if ($session->finished_at === null) {
            throw new LogicException('Сессия должна иметь дату завершения.');
        }

        return Duration::fromDates($session->started_at, $session->finished_at);
    }

    /**
     * @return Collection<int, ReviewSessionItem>
     */
    private function getSessionItems(ReviewSession $session): Collection
    {
        if ($session->items->isEmpty()) {
            throw new LogicException('Сессия не может быть пустой.');
        }

        return $session->items;
    }

    /**
     * @param Collection<int, ReviewSessionItem> $items
     * @return Collection<int, int>
     */
    private function getResponseTimes(Collection $items): Collection
    {
        return $items->map(function (ReviewSessionItem $item) {
            if ($item->presented_at === null || $item->answered_at === null) {
                throw new LogicException('Все элементы сессии должны иметь дату показа и ответа');
            }

            return (int)$item->presented_at->diffInSeconds($item->answered_at, true);
        });
    }
}