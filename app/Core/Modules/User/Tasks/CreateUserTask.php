<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\User\Models\User;
use Throwable;

final class CreateUserTask extends Task
{
    /**
     * @throws Throwable
     */
    public function run(string $name, ?LanguageLevel $level = null): User
    {
        $user = new User();
        $user->name = $name;
        $user->level = $level;
        $user->saveOrFail();

        return $user;
    }
}