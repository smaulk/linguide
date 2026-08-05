<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Term\Dto\ReviewTermDto;
use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\Term\Mappers\LearningProgressMapper;
use App\Core\Modules\Term\Models\LearningProgress;
use App\Core\Modules\Term\Models\ReviewSessionItem;
use Illuminate\Support\Facades\DB;
use Throwable;

final class GetTermFromReviewSessionAction extends Action
{
    public function __construct(private readonly LearningProgressMapper $mapper){}

    /**
     * @throws Throwable
     */
    public function run(int $sessionId, ?UtcOffset $utcOffset = null): ?ReviewTermDto
    {
        [$sessionItem, $learningProgress] = DB::transaction(function () use ($sessionId) {
            $sessionItem = $this->getSessionItem($sessionId);
            if ($sessionItem === null) {
                return [null, null];
            }

            $this->markAsPresented($sessionItem->id);

            return [
                $sessionItem,
                $this->getLearningProgress($sessionItem->session->user_id, $sessionItem->variant_id),
            ];
        });

        if ($sessionItem === null || $learningProgress === null) {
            return null;
        }

        return new ReviewTermDto(
            learningProgress: $this->mapper->mapLearningProgressModelToDto($learningProgress, $utcOffset),
            mode: $sessionItem->mode,
        );
    }

    private function getSessionItem(int $sessionId): ?ReviewSessionItem
    {
        return ReviewSessionItem::query()
            ->select(['id', 'session_id', 'variant_id', 'mode'])
            ->where('session_id', $sessionId)
            ->whereNull('answered_at')
            ->with(['session:id,user_id'])
            ->orderBy('id')
            ->lockForUpdate()
            ->first();
    }

    /**
     * @throws Throwable
     */
    private function markAsPresented(int $sessionItemId): void
    {
        ReviewSessionItem::query()
            ->where('id', $sessionItemId)
            ->whereNull('presented_at')
            ->update([
                'presented_at' => now(),
            ]);
    }

    private function getLearningProgress(int $userId, int $variantId): LearningProgress
    {
        return LearningProgress::query()
            ->where('user_id', $userId)
            ->where('variant_id', $variantId)
            ->with([
                'variant',
                'variant.term:id,text,type',
                'variant.translations',
                'variant.translations.examples',
            ])
            ->firstOrFail();
    }
}