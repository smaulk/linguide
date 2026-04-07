<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\Dictionary\Dto\WordProgressDto;
use App\Core\Modules\Dictionary\Mappers\WordMapper;
use App\Core\Modules\Dictionary\Models\UserWordProgress;
use App\Core\Modules\Dictionary\Models\WordReviewSessionItem;
use Illuminate\Support\Facades\DB;
use Throwable;

final class GetWordFromReviewSessionAction extends Action
{
    public function __construct(private readonly WordMapper $mapper){}

    /**
     * @throws Throwable
     */
    public function run(int $sessionId, ?UtcOffset $utcOffset = null): ?WordProgressDto
    {
        $wordProgress = DB::transaction(function () use ($sessionId) {
            $sessionItem = $this->getSessionItem($sessionId);
            if ($sessionItem === null) {
                return null;
            }

            $this->markAsPresented($sessionItem->id);

            return $this->getWordProgress(
                $sessionItem->session->user_id,
                $sessionItem->word_id
            );
        });

        return $wordProgress !== null
            ? $this->mapper->mapWordProgressModelToDto($wordProgress, $utcOffset)
            : null;
    }

    private function getSessionItem(int $sessionId): ?WordReviewSessionItem
    {
        return WordReviewSessionItem::query()
            ->select(['id', 'session_id', 'word_id'])
            ->where('session_id', $sessionId)
            ->whereNull('answered_at')
            ->with([
                'session:id,user_id'
            ])
            ->orderBy('id')
            ->lockForUpdate()
            ->first();
    }

    /**
     * @throws Throwable
     */
    private function markAsPresented(int $sessionItemId): void
    {
        WordReviewSessionItem::query()
            ->where('id', $sessionItemId)
            ->whereNull('presented_at')
            ->update([
                'presented_at' => now(),
            ]);
    }

    private function getWordProgress(int $userId, int $wordId): UserWordProgress
    {
        return UserWordProgress::query()
            ->where('user_id', $userId)
            ->where('word_id', $wordId)
            ->with([
                'word',
                'word.translations',
                'word.translations.examples',
            ])
            ->firstOrFail();
    }
}