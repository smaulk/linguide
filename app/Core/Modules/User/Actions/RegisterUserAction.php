<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\RegisterUserDto;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;
use App\Core\Modules\User\Mappers\UserMapper;
use App\Core\Modules\User\Tasks\CreateUserIdentityTask;
use App\Core\Modules\User\Tasks\CreateUserSettingTask;
use App\Core\Modules\User\Tasks\CreateUserTask;
use Illuminate\Support\Facades\DB;
use LogicException;
use Throwable;

final class RegisterUserAction extends Action
{
    public function __construct(
        private readonly CreateUserTask $createUserTask,
        private readonly CreateUserIdentityTask $createUserIdentityTask,
        private readonly CreateUserSettingTask $createUserSettingTask,
        private readonly UserMapper $mapper,
    ){}

    /**
     * @throws Throwable
     * @throws InvalidUserDataException
     * @throws LogicException
     */
    public function run(RegisterUserDto $dto): UserDto
    {
        $user = DB::transaction(function () use ($dto) {
            $user = $this->createUserTask->run($dto->name, $dto->status);
            $settings = $this->createUserSettingTask->run($user->id);
            $this->createUserIdentityTask->run($user->id, $dto->identity);

            $user->setRelation('settings', $settings);

            return $user;
        });

        return $this->mapper->mapUserModelToDto($user);
    }
}