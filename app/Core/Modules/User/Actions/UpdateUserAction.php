<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Models\User;
use Throwable;

final class UpdateUserAction extends Action
{
    /**
     * @throws Throwable
     */
    public function run(UserDto $dto): void
    {
        $user = $this->getUserById($dto->id);
        $user->name = $dto->name;
        $user->level = $dto->level;
        $user->saveOrFail();
    }

    private function getUserById(int $userId): User
    {
        return User::query()->findOrFail($userId);
    }
}