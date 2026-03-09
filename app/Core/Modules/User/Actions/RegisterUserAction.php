<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\RegisterUserDto;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;
use App\Core\Modules\User\Tasks\CreateUserIdentityTask;
use App\Core\Modules\User\Tasks\CreateUserTask;
use Illuminate\Support\Facades\DB;
use Throwable;

final class RegisterUserAction extends Action
{
    public function __construct(
        private readonly CreateUserTask $createUserTask,
        private readonly CreateUserIdentityTask $createUserIdentityTask,
    ){}

    /**
     * @throws Throwable
     * @throws InvalidUserDataException
     */
    public function run(RegisterUserDto $dto): UserDto
    {
        $user = DB::transaction(function () use ($dto) {
            $user = $this->createUserTask->run($dto->name, $dto->languageLevel);
            $this->createUserIdentityTask->run($user->id, $dto->identity);

            return $user;
        });

        return new UserDto($user->id, $user->name, $user->level);
    }
}