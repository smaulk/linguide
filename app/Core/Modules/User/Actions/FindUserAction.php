<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\FindUserDto;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Enums\UserProviderType;
use App\Core\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

final class FindUserAction extends Action
{
    public function run(FindUserDto $dto): ?UserDto
    {
        $user = $this
            ->setConditions(User::query(), $dto)
            ->first();

        return $user !== null
            ? new UserDto($user->id, $user->name)
            : null;
    }

    /**
     * @param Builder<User> $query
     * @param FindUserDto $dto
     * @return Builder<User>
     */
    public function setConditions(Builder $query, FindUserDto $dto): Builder
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