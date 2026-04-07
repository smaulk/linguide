<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Models\User;
use App\Core\Modules\User\Models\UserSetting;
use App\Core\Modules\User\Vo\WordsReviewLimit;
use App\Core\Modules\Dictionary\Tasks\CreateWordReviewSessionItemsTask;
use App\Core\Modules\Dictionary\Tasks\CreateWordReviewSessionTask;
use App\Core\Modules\Dictionary\Tasks\GetActiveWordReviewSessionTask;
use App\Core\Modules\Dictionary\Tasks\GetWordIdsToReviewTask;
use Illuminate\Support\Facades\DB;
use Throwable;

final class StartWordReviewSessionAction extends Action
{
    public function __construct(
        private readonly GetActiveWordReviewSessionTask $getActiveSessionTask,
        private readonly CreateWordReviewSessionTask $createSessionTask,
        private readonly CreateWordReviewSessionItemsTask $createSessionItemsTask,
        private readonly GetWordIdsToReviewTask $getWordIdsTask,
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

            $wordIds = $this->getWordIds($user->id, $user->settingsOrFail());
            if ($wordIds === []) {
                return null;
            }

            $session = $this->createSessionTask->run($user->id);
            $this->createSessionItemsTask->run($session->id, $wordIds);

            return $session->id;
        });
    }

    private function getUser(int $userId): User
    {
        return User::query()
            ->select(['id'])
            ->with(['settings:user_id,words_review_limit'])
            ->findOrFail($userId);
    }


    /**
     * @return int[]
     * @throws Throwable
     */
    private function getWordIds(int $userId, UserSetting $settings): array
    {
        return $this->getWordIdsTask->run(
            $userId,
            WordsReviewLimit::fromInt($settings->words_review_limit)
        );
    }
}