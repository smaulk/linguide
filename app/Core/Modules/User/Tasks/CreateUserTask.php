<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\User\Enums\UserStatus;
use App\Core\Modules\User\Models\User;
use Throwable;

final class CreateUserTask extends Task
{
    /**
     * @throws Throwable
     */
    public function run(string $name, UserStatus $status = UserStatus::INACTIVE): User
    {
        $user = new User();
        $user->name = $name;
        $user->status = $status;
        $user->saveOrFail();

        return $user;
    }
}