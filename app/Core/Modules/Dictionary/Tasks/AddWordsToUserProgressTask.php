<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\User\Models\User;
use App\Core\Modules\Dictionary\Enums\PartOfSpeech;
use App\Core\Modules\Dictionary\Models\UserWordProgress;
use App\Core\Modules\Dictionary\Models\Word;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use LogicException;
use Throwable;

final class AddWordsToUserProgressTask extends Task
{
    /**
     * @throws Throwable
     */
    public function run(int $userId, int $count): void
    {
        $user = $this->getUser($userId);
        $userLevel = $this->getUserLevel($user);

        $words = $this->getWords($user->id, $userLevel, $count);

        $progressData = $this->prepareProgressData($user->id, $words);

        UserWordProgress::query()->insert($progressData);
    }

    private function getUser(int $userId): User
    {
        return User::query()
            ->select(['id'])
            ->with(['settings:user_id,level'])
            ->findOrFail($userId);
    }

    /**
     * @throws LogicException
     */
    private function getUserLevel(User $user): LanguageLevel
    {
        $level = $user->settingsOrFail()->level;
        if ($level === null) {
            throw new LogicException('User has not set the level');
        }

        return $level;
    }

    /**
     * @return Collection<int, Word>
     */
    private function getWords(int $userId, LanguageLevel $level, int $count): Collection
    {
        return Word::query()
            ->select(['id'])
            ->whereIn('pos', PartOfSpeech::trainable())
            ->whereNotExists(function (Builder $q) use ($userId) {
                $q->selectRaw('1')
                    ->from('user_word_progress')
                    ->whereColumn('user_word_progress.word_id', 'words.id')
                    ->where('user_word_progress.user_id', $userId);
            })
            ->where('level', '>=', $level)
            ->orderBy('level')
            ->orderByRaw('RANDOM()')
            ->limit($count)
            ->get();
    }

    /**
     * @param int $userId
     * @param Collection<int, Word> $words
     * @return array<int, array<string, mixed>>
     */
    private function prepareProgressData(int $userId, Collection $words): array
    {
        $now = now();

        return $words->map(function (Word $word) use ($userId, $now) {
            return [
                'user_id'     => $userId,
                'word_id'     => $word->id,
                'repetitions' => 0,
                'interval'    => 0,
                'ease_factor' => UserWordProgress::DEFAULT_EASE_FACTOR,
                'due_at'      => $now,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        })->all();
    }
}