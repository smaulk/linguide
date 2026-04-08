<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\SubActions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Dictionary\Models\LearningProgress;
use App\Core\Modules\Dictionary\Tasks\AddTermsToLearningTask;
use App\Core\Modules\User\Vo\ReviewLimit;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

final class GetTermVariantsIdsToReviewSubAction extends Action
{
    public function __construct(private readonly AddTermsToLearningTask $addTermsTask){}

    /**
     * @return int[]
     * @throws Throwable
     */
    public function run(int $userId, ReviewLimit $reviewLimit): array
    {
        $userReviewLimit = $reviewLimit->value();
        $learningTermsCount = $this->getLearningTermsCount($userId);

        if ($learningTermsCount < $userReviewLimit) {
            $this->addTermsTask->run($userId, $userReviewLimit - $learningTermsCount);
        }

        $learningTerms = $this->getRandomTerms($userId, $userReviewLimit);

        return $learningTerms->pluck('variant_id')->all();
    }


    private function getLearningTermsCount(int $userId): int
    {
        return LearningProgress::query()
            ->where('user_id', $userId)
            ->where('due_at', '<=', now())
            ->count();
    }

    /**
     * @return Collection<int, LearningProgress>
     */
    private function getRandomTerms(int $userId, int $count): Collection
    {
        return LearningProgress::query()
            ->select(['id', 'variant_id'])
            ->where('user_id', $userId)
            ->where('due_at', '<=', now())
            ->oldest('due_at')
            ->limit($count)
            ->get();
    }
}