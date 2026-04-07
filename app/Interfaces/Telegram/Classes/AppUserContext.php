<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Classes;

use App\Core\Modules\User\Dto\UserDto;
use RuntimeException;
use SergiX44\Nutgram\Nutgram;

final class AppUserContext
{
    private const string KEY = 'appUser';

    public function set(Nutgram $bot, UserDto $user): void
    {
        $bot->set(self::KEY, $user);
    }

    /**
     * @throws RuntimeException
     */
    public function get(Nutgram $bot): UserDto
    {
        $user = $bot->get(self::KEY);

        if (!($user instanceof UserDto)) {
            throw new RuntimeException('User is not defined');
        }

        return $user;
    }
}