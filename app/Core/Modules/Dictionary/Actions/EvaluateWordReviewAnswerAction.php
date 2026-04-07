<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Dictionary\Enums\WordReviewAnswerResult;
use App\Core\Modules\Dictionary\Models\UserWordProgress;
use App\Core\Modules\Dictionary\Models\WordReviewSession;
use App\Core\Modules\Dictionary\Tasks\CalculateWordProgressTask;
use App\Core\Modules\Dictionary\Tasks\FinishWordReviewSessionIfCompletedTask;
use App\Core\Modules\Dictionary\Tasks\SaveSessionWordReviewAnswerTask;
use Illuminate\Support\Facades\DB;
use Throwable;

final class EvaluateWordReviewAnswerAction extends Action
{
    public function __construct(
        private readonly SaveSessionWordReviewAnswerTask $saveSessionAnswerTask,
        private readonly CalculateWordProgressTask $calculateProgressTask,
        private readonly FinishWordReviewSessionIfCompletedTask $finishSessionIfCompletedTask,
    ){}

    /**
     * @throws Throwable
     */
    public function run(int $sessionId, int $wordId, WordReviewAnswerResult $result): bool
    {
        return DB::transaction(function () use ($sessionId, $wordId, $result) {
            $session = $this->getSession($sessionId);
            $this->saveSessionAnswerTask->run($session->id, $wordId, $result);
            $this->saveProgressAnswer($session->user_id, $wordId, $result);

            return $this->finishSessionIfCompletedTask->run($session->id);
        });
    }

    private function getSession(int $sessionId): WordReviewSession
    {
        return WordReviewSession::query()
            ->select(['id', 'user_id'])
            ->lockForUpdate()
            ->findOrFail($sessionId);
    }

    /**
     * @throws Throwable
     */
    private function saveProgressAnswer(int $userId, int $wordId, WordReviewAnswerResult $result): void
    {
        $wordProgress = $this->getWordProgress($userId, $wordId);
        $this->calculateProgressTask->run($wordProgress, $result);
        $wordProgress->saveOrFail();
    }

    private function getWordProgress(int $userId, int $wordId): UserWordProgress
    {
        return UserWordProgress::query()
            ->where('user_id', $userId)
            ->where('word_id', $wordId)
            ->firstOrFail();
    }
}