<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Vo\WordsReviewLimit;
use App\Core\Modules\Dictionary\Models\UserWordProgress;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

final class GetWordIdsToReviewTask extends Action
{
    public function __construct(private readonly AddWordsToUserProgressTask $addWordsTask){}

    /**
     * @return int[]
     * @throws Throwable
     */
    public function run(int $userId, WordsReviewLimit $wordsLimit): array
    {
        $userWordsLimit = $wordsLimit->value();
        $userWordsCount = $this->getUserWordsCount($userId);

        if ($userWordsCount < $userWordsLimit) {
            $this->addWordsTask->run($userId, $userWordsLimit - $userWordsCount);
        }

        $wordsProgress = $this->getRandomWords($userId, $userWordsLimit);

        return $wordsProgress->pluck('word_id')->all();
    }


    private function getUserWordsCount(int $userId): int
    {
        return UserWordProgress::query()
            ->where('user_id', $userId)
            ->where('due_at', '<=', now())
            ->count();
    }

    /**
     * @return Collection<int, UserWordProgress>
     */
    private function getRandomWords(int $userId, int $count): Collection
    {
        return UserWordProgress::query()
            ->select(['id', 'word_id'])
            ->where('user_id', $userId)
            ->where('due_at', '<=', now())
            ->orderBy('due_at')
            ->limit($count)
            ->get();
    }
}