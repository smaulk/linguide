<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Base\Action;
use App\Core\Modules\User\Dto\CreateUserDto;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;
use App\Core\Modules\User\Models\User;
use App\Core\Modules\User\Tasks\CreateUserIdentityTask;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CreateUserAction extends Action
{
    /**
     * @throws Throwable
     * @throws InvalidUserDataException
     */
    public function run(CreateUserDto $dto): UserDto
    {
        $user =  DB::transaction(function () use ($dto) {
            $user = $this->createUser($dto);
            $this->task(CreateUserIdentityTask::class)->run($user->id, $dto->identity);

            return $user;
        });

        return new UserDto($user->id, $user->name);
    }

    /**
     * @throws Throwable
     */
    private function createUser(CreateUserDto $dto): User
    {
        $user = new User();
        $user->name = $dto->name;
        $user->saveOrFail();

        return $user;
    }
}