<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\User\Dto\UserSettingsDto;
use App\Core\Modules\User\Models\UserSetting;
use Throwable;

final class CreateUserSettingTask extends Task
{
    /**
     * @throws Throwable
     */
    public function run(int $userId, ?UserSettingsDto $settingDto = null): UserSetting
    {
        $userSetting = new UserSetting();
        $userSetting->user_id = $userId;
        $userSetting->review_limit = UserSetting::REVIEW_LIMIT_DEFAULT;

        if ($settingDto !== null) {
            $userSetting->level = $settingDto->level;
            $userSetting->utc_offset = $settingDto->utcOffset?->value();
            $userSetting->review_limit = $settingDto->reviewLimit->value();
        }

        $userSetting->saveOrFail();

        return $userSetting;
    }
}