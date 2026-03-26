<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Dto\RegisterUserDto;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingDto;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;
use App\Core\Modules\User\Tasks\CreateUserIdentityTask;
use App\Core\Modules\User\Tasks\CreateUserSettingTask;
use App\Core\Modules\User\Tasks\CreateUserTask;
use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\User\Vo\WordsRepeatLimit;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

final class RegisterUserAction extends Action
{
    public function __construct(
        private readonly CreateUserTask $createUserTask,
        private readonly CreateUserIdentityTask $createUserIdentityTask,
        private readonly CreateUserSettingTask $createUserSettingTask,
    ){}

    /**
     * @throws Throwable
     * @throws InvalidUserDataException
     */
    public function run(RegisterUserDto $dto): UserDto
    {
        $user = DB::transaction(function () use ($dto) {
            $user = $this->createUserTask->run($dto->name);
            $settings = $this->createUserSettingTask->run($user->id);
            $this->createUserIdentityTask->run($user->id, $dto->identity);

            $user->setRelation('settings', $settings);

            return $user;
        });

        $settings = $user->settingsOrFail();
        $utcOffset = $settings->utc_offset !== null
            ? UtcOffset::fromInt($settings->utc_offset)
            : null;

        return new UserDto(
            id: $user->id,
            name: $user->name,
            settings: new UserSettingDto(
                level: $settings->level,
                utcOffset: $utcOffset,
                wordsRepeatLimit: WordsRepeatLimit::fromInt($settings->words_repeat_limit),
            ),
        );
    }
}