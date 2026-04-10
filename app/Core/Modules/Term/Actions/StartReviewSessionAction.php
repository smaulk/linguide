<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Term\SubActions\GetTermVariantsIdsToReviewSubAction;
use App\Core\Modules\Term\Tasks\CreateReviewSessionItemsTask;
use App\Core\Modules\Term\Tasks\CreateReviewSessionTask;
use App\Core\Modules\Term\Tasks\GetActiveReviewSessionTask;
use App\Core\Modules\User\Models\User;
use App\Core\Modules\User\Models\UserSetting;
use App\Core\Modules\User\Vo\ReviewLimit;
use Illuminate\Support\Facades\DB;
use Throwable;

final class StartReviewSessionAction extends Action
{
    public function __construct(
        private readonly GetActiveReviewSessionTask $getActiveSessionTask,
        private readonly CreateReviewSessionTask $createSessionTask,
        private readonly CreateReviewSessionItemsTask $createSessionItemsTask,
        private readonly GetTermVariantsIdsToReviewSubAction $getVariantsIdsSubAction,
    ){}

    /**
     * @return int|null review session id
     * @throws Throwable
     */
    public function run(int $userId): ?int
    {
        $activeSession = $this->getActiveSessionTask->run($userId);
        if ($activeSession !== null) {
            return $activeSession->id;
        }

        return DB::transaction(function () use ($userId) {
            $user = $this->getUser($userId);
            $settings = $user->settingsOrFail();

            $variantsIds = $this->getVariantsIds($user->id, $settings->review_limit);
            if ($variantsIds === []) {
                return null;
            }

            $session = $this->createSessionTask->run($user->id);
            $this->createSessionItemsTask->run($session->id, $variantsIds);

            return $session->id;
        });
    }

    private function getUser(int $userId): User
    {
        return User::query()
            ->select(['id'])
            ->with(['settings:user_id,review_limit'])
            ->findOrFail($userId);
    }


    /**
     * @return int[]
     * @throws Throwable
     */
    private function getVariantsIds(int $userId, int $reviewLimit): array
    {
        return $this->getVariantsIdsSubAction->run(
            $userId,
            ReviewLimit::fromInt($reviewLimit)
        );
    }
}