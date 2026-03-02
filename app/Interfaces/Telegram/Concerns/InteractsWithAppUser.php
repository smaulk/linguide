<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Concerns;

use App\Core\Modules\User\Dto\UserDto;
use RuntimeException;
use SergiX44\Nutgram\Nutgram;

trait InteractsWithAppUser
{
    protected function getAppUser(Nutgram $bot): UserDto
    {
        $appUser = $bot->get('appUser');
        if (!($appUser instanceof UserDto)) {
            throw new RuntimeException('User is not defined');
        }

        return $appUser;
    }
}