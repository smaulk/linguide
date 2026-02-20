<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Concerns;

use RuntimeException;
use SergiX44\Nutgram\Nutgram;

trait InteractsWithAppUser
{
    protected function getAppUserId(Nutgram $bot): int
    {
        $appUserId = $bot->get('appUserId');
        if (!is_int($appUserId)) {
            throw new RuntimeException('User is not defined');
        }

        return $appUserId;
    }
}