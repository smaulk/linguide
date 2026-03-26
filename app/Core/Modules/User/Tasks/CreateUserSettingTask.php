<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\User\Dto\UserSettingDto;
use App\Core\Modules\User\Models\UserSetting;
use Throwable;

final class CreateUserSettingTask extends Task
{
    /**
     * @throws Throwable
     */
    public function run(int $userId, ?UserSettingDto $settingDto = null): UserSetting
    {
        $userSetting = new UserSetting();
        $userSetting->user_id = $userId;
        $userSetting->words_repeat_limit = UserSetting::WORD_REPEAT_LIMIT_DEFAULT;

        if ($settingDto !== null) {
            $userSetting->level = $settingDto->level;
            $userSetting->utc_offset = $settingDto->utcOffset?->value();
            $userSetting->words_repeat_limit = $settingDto->wordsRepeatLimit->value();
        }

        $userSetting->saveOrFail();

        return $userSetting;
    }
}