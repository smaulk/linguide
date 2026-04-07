<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Mappers\UserMapper;
use App\Core\Modules\User\Models\User;
use LogicException;

final class FindUserByIdAction extends Action
{
    public function __construct(private readonly UserMapper $mapper){}

    /**
     * @throws LogicException
     */
    public function run(int $userId): ?UserDto
    {
        $user = User::query()
            ->with(['settings'])
            ->find($userId);

        return $user !== null
            ? $this->mapper->mapUserModelToDto($user)
            : null;
    }
}