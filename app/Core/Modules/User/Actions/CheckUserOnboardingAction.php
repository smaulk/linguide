<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\UserDto;

final class CheckUserOnboardingAction extends Action
{
    /**
     * Проверяет, прошел ли пользователь онбординг
     */
    public function run(UserDto $userDto): bool
    {
        return $userDto->level !== null;
    }
}