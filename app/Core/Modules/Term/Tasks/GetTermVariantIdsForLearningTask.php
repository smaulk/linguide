<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Enums\PartOfSpeech;
use App\Core\Modules\Term\Models\TermVariant;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\User\Models\User;
use Illuminate\Database\Query\Builder;
use LogicException;

final class GetTermVariantIdsForLearningTask extends Task
{
    /**
     * @param int $userId
     * @param int $count
     * @return int[]
     */
    public function run(int $userId, int $count): array
    {
        $user = $this->getUser($userId);
        $userLevel = $this->getUserLevel($user);

        return $this->getVariants($user->id, $userLevel, $count);
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
     * Возвращает id вариантов терминов для добавления в обучение.
     *
     * Берем только нужные части речи (pos), берем только верефицированные термины (is_verified).
     * Проверяем, что этого варианта термина еще нет в прогрессе.
     * Сортируем по уровню, берем от текущего уровня и выше, и случайно перемешиваем.
     *
     * @return int[]
     */
    private function getVariants(int $userId, LanguageLevel $level, int $count): array
    {
        return TermVariant::query()
            ->whereIn('pos', PartOfSpeech::trainable())
            ->join('terms', 'terms.id', '=', 'term_variants.term_id')
            ->where('terms.is_verified', true)
            ->whereNotExists(function (Builder $q) use ($userId) {
                $q->selectRaw('1')
                    ->from('learning_progress')
                    ->whereColumn('learning_progress.variant_id', '=', 'term_variants.id')
                    ->where('learning_progress.user_id', $userId);
            })
            ->where('level', '>=', $level)
            ->orderBy('level')
            ->orderByRaw('RANDOM()')
            ->limit($count)
            ->pluck('term_variants.id')
            ->all();
    }
}