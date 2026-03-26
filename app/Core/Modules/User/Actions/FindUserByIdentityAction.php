<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\FindUserByIdentityDto;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingDto;
use App\Core\Modules\User\Enums\UserProviderType;
use App\Core\Modules\User\Models\User;
use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\User\Vo\WordsRepeatLimit;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

final class FindUserByIdentityAction extends Action
{
    /**
     * @throws Throwable
     */
    public function run(FindUserByIdentityDto $dto): ?UserDto
    {
        $user = $this
            ->setConditions(User::query(), $dto)
            ->with(['settings'])
            ->first();

        if ($user === null) {
            return null;
        }

        $settings = $user->settingsOrFail();
        $utcOffset = $settings->utc_offset !== null
            ? UtcOffset::fromInt($settings->utc_offset)
            : null;

        return new UserDto(
            id: $user->id,
            name: $user->name,
            settings: new UserSettingDto(
                level: $settings->level,
                utcOffset: $utcOffset,
                wordsRepeatLimit: WordsRepeatLimit::fromInt($settings->words_repeat_limit),
            ),
        );
    }

    /**
     * @param Builder<User> $query
     * @param FindUserByIdentityDto $dto
     * @return Builder<User>
     */
    public function setConditions(Builder $query, FindUserByIdentityDto $dto): Builder
    {
        return $query->whereHas('identities', function (Builder $query) use ($dto) {
            $query->where('provider', $dto->providerType);

            if ($dto->providerType === UserProviderType::EMAIL && $dto->email) {
                $query->where('email', $dto->email);
            } else if ($dto->providerUserId) {
                $query->where('provider_user_id', $dto->providerUserId);
            }
        });
    }
}