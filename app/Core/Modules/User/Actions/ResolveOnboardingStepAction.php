<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingsDto;
use App\Core\Modules\User\Enums\UserOnboardingStep;

final class ResolveOnboardingStepAction extends Action
{
    /**
     * Проверяет, прошел ли пользователь онбординг
     */
    public function run(UserSettingsDto $settings): UserOnboardingStep
    {
        if ($settings->level === null) {
            return UserOnboardingStep::ASK_LEVEL;
        }
        if ($settings->utcOffset === null) {
            return UserOnboardingStep::ASK_TIMEZONE;
        }

        return UserOnboardingStep::COMPLETED;
    }
}