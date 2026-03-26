<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingDto;
use App\Core\Modules\User\Enums\UserOnboardingStatus;

final class CheckUserOnboardingAction extends Action
{
    /**
     * Проверяет, прошел ли пользователь онбординг
     */
    public function run(UserSettingDto $settings): UserOnboardingStatus
    {
        if ($settings->level === null) {
            return UserOnboardingStatus::SELECT_LEVEL;
        }
        if ($settings->utcOffset === null) {
            return UserOnboardingStatus::SELECT_TIMEZONE;
        }

        return UserOnboardingStatus::SUCCESS;
    }
}