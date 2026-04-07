<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\FindUserByIdentityDto;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Enums\UserProviderType;
use App\Core\Modules\User\Mappers\UserMapper;
use App\Core\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use LogicException;

final class FindUserByIdentityAction extends Action
{
    public function __construct(private readonly UserMapper $mapper){}

    /**
     * @throws LogicException
     */
    public function run(FindUserByIdentityDto $dto): ?UserDto
    {
        $user = $this
            ->setConditions(User::query(), $dto)
            ->with(['settings'])
            ->first();

        return $user !== null
            ? $this->mapper->mapUserModelToDto($user)
            : null;
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