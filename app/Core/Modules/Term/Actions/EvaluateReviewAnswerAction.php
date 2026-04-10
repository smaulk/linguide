<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Term\Enums\ReviewAnswerResult;
use App\Core\Modules\Term\Models\LearningProgress;
use App\Core\Modules\Term\Models\ReviewSession;
use App\Core\Modules\Term\Tasks\CalculateLearningProgressTask;
use App\Core\Modules\Term\Tasks\FinishReviewSessionIfCompletedTask;
use App\Core\Modules\Term\Tasks\SaveReviewSessionAnswerTask;
use Illuminate\Support\Facades\DB;
use Throwable;

final class EvaluateReviewAnswerAction extends Action
{
    public function __construct(
        private readonly SaveReviewSessionAnswerTask $saveSessionAnswerTask,
        private readonly CalculateLearningProgressTask $calculateProgressTask,
        private readonly FinishReviewSessionIfCompletedTask $finishSessionIfCompletedTask,
    ){}

    /**
     * @throws Throwable
     */
    public function run(int $sessionId, int $termVariantId, ReviewAnswerResult $result): bool
    {
        return DB::transaction(function () use ($sessionId, $termVariantId, $result) {
            $session = $this->getSession($sessionId);
            $this->saveSessionAnswerTask->run($session->id, $termVariantId, $result);
            $this->saveProgressAnswer($session->user_id, $termVariantId, $result);

            return $this->finishSessionIfCompletedTask->run($session->id);
        });
    }

    private function getSession(int $sessionId): ReviewSession
    {
        return ReviewSession::query()
            ->select(['id', 'user_id'])
            ->lockForUpdate()
            ->findOrFail($sessionId);
    }

    /**
     * @throws Throwable
     */
    private function saveProgressAnswer(int $userId, int $termVariantId, ReviewAnswerResult $result): void
    {
        $learningProgress = $this->getLearningProgress($userId, $termVariantId);
        $this->calculateProgressTask->run($learningProgress, $result);
        $learningProgress->saveOrFail();
    }

    private function getLearningProgress(int $userId, int $termVariantId): LearningProgress
    {
        return LearningProgress::query()
            ->where('user_id', $userId)
            ->where('variant_id', $termVariantId)
            ->firstOrFail();
    }
}