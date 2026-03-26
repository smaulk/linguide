<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Models\User;
use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\User\Vo\WordsRepeatLimit;
use App\Core\Modules\Words\Dto\WordProgressDto;
use App\Core\Modules\Words\Mappers\WordMapper;
use App\Core\Modules\Words\Models\UserWordProgress;
use App\Core\Modules\Words\Tasks\AddWordsToUserProgressTask;
use DateTimeZone;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

final class GetWordsToRepeatAction extends Action
{
    public function __construct(
        private readonly AddWordsToUserProgressTask $addWordsTask,
        private readonly WordMapper $mapper,
    ){}

    /**
     * @return WordProgressDto[]
     * @throws Throwable
     */
    public function run(int $userId): array
    {
        $user = $this->getUser($userId);
        $settings = $user->settingsOrFail();

        $userWordsCount = $this->getUserWordsCount($user->id);
        $userWordsLimit = WordsRepeatLimit::fromInt($settings->words_repeat_limit);
        if ($userWordsCount < $userWordsLimit->value()) {
            $this->addWordsTask->run($userId, $userWordsLimit->value() - $userWordsCount);
        }

        $userWordProgress = $this->getRandomWords($user->id, $userWordsLimit->value());
        $utcOffset = $settings->utc_offset !== null
            ? UtcOffset::fromInt($settings->utc_offset)
            : null;

        return $this->mapper
            ->mapCollectionWordProgressModelToDto($userWordProgress, $utcOffset)
            ->all();
    }

    private function getUser(int $userId): User
    {
        return User::query()
            ->with(['settings'])
            ->findOrFail($userId);
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
            ->where('user_id', $userId)
            ->where('due_at', '<=', now())
            ->with([
                'word',
                'word.translations',
                'word.translations.examples',
            ])
            ->orderBy('due_at')
            ->limit($count)
            ->get();
    }
}