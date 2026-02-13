<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Concerns;

use RuntimeException;
use SergiX44\Nutgram\Nutgram;

trait InteractsWithAppUser
{
    private int $appUserId;

    private function setAppUserId(Nutgram $bot): void
    {
        $appUserId = $bot->get('appUserId');
        if (!is_int($appUserId)) {
            throw new RuntimeException('Пользователь не определен');
        }
        $this->appUserId = $appUserId;
    }

    public function getAppUserId(): int
    {
        return $this->appUserId;
    }
}