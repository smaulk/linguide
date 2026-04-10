<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Models\TermVariant;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\User\Models\User;
use App\Core\Modules\Term\Enums\PartOfSpeech;
use App\Core\Modules\Term\Models\LearningProgress;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use LogicException;
use Throwable;

final class AddTermsToLearningTask extends Task
{
    /**
     * @throws Throwable
     */
    public function run(int $userId, int $count): void
    {
        $user = $this->getUser($userId);
        $userLevel = $this->getUserLevel($user);

        $variants = $this->getVariants($user->id, $userLevel, $count);

        LearningProgress::query()->insert(
            $this->prepareProgressData($user->id, $variants)
        );
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
     * Возвращает варианты терминов для добавления в обучение.
     *
     * Берем только нужные части речи (pos), берем только верефицированные термины (is_verified).
     * Проверяем, что этого варианта термина еще нет в прогрессе.
     * Сортируем по уровню, берем от текущего уровня и выше, и случайно перемешиваем.
     *
     * @return Collection<int, TermVariant>
     */
    private function getVariants(int $userId, LanguageLevel $level, int $count): Collection
    {
        return TermVariant::query()
            ->select(['term_variants.id', 'term_variants.term_id'])
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
            ->get();
    }

    /**
     * @param int $userId
     * @param Collection<int, TermVariant> $variants
     * @return array<int, array<string, mixed>>
     */
    private function prepareProgressData(int $userId, Collection $variants): array
    {
        $now = now();

        return $variants->map(function (TermVariant $variant) use ($userId, $now) {
            return [
                'user_id'     => $userId,
                'variant_id'  => $variant->id,
                'repetitions' => 0,
                'interval'    => 0,
                'ease_factor' => LearningProgress::DEFAULT_EASE_FACTOR,
                'due_at'      => $now,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        })->all();
    }
}