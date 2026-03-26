<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingDto;
use App\Core\Modules\User\Models\User;
use App\Core\Modules\User\Models\UserSetting;
use Throwable;

final class UpdateUserSettingAction extends Action
{
    /**
     * @throws Throwable
     */
    public function run(int $userId, UserSettingDto $dto): void
    {
        $userSetting = $this->getUserSettingById($userId);

        $userSetting->level = $dto->level;
        $userSetting->utc_offset = $dto->utcOffset?->value();
        $userSetting->words_repeat_limit = $dto->wordsRepeatLimit->value();

        $userSetting->saveOrFail();
    }

    private function getUserSettingById(int $userId): UserSetting
    {
        return UserSetting::query()->findOrFail($userId);
    }
}