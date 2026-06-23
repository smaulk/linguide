<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\SubActions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Term\Dto\StoreLearningProgressDto;
use App\Core\Modules\Term\Models\LearningProgress;
use App\Core\Modules\Term\Tasks\GetTermVariantIdsForLearningTask;
use App\Core\Modules\Term\Tasks\StoreLearningProgressTask;
use App\Core\Modules\User\Vo\ReviewLimit;
use Throwable;

final class GetTermVariantIdsToReviewSubAction extends Action
{
    public function __construct(
        private readonly GetTermVariantIdsForLearningTask $getTermVarIdsTask,
        private readonly StoreLearningProgressTask $storeLearningProgressTask,
    ){}

    /**
     * @return int[]
     * @throws Throwable
     */
    public function run(int $userId, ReviewLimit $reviewLimit): array
    {
        $userReviewLimit = $reviewLimit->value();
        $termsCount = $this->getTermsForLearningCount($userId);

        if ($termsCount < $userReviewLimit) {
            $termVarIds = $this->getTermVarIdsTask->run($userId, $userReviewLimit - $termsCount);
            $this->storeLearningProgressTask->run([
                new StoreLearningProgressDto(
                    userId: $userId,
                    termVariantIds: $termVarIds,
                ),
            ]);
        }

        return $this->getTermsForLearning($userId, $userReviewLimit);
    }

    private function getTermsForLearningCount(int $userId): int
    {
        return LearningProgress::query()
            ->where('user_id', $userId)
            ->where('due_at', '<=', now())
            ->whereHas('variant.translations')
            ->count();
    }

    /**
     * @return int[]
     */
    private function getTermsForLearning(int $userId, int $count): array
    {
        return LearningProgress::query()
            ->where('user_id', $userId)
            ->where('due_at', '<=', now())
            ->whereHas('variant.translations')
            ->oldest('due_at')
            ->limit($count)
            ->pluck('variant_id')
            ->all();
    }
}