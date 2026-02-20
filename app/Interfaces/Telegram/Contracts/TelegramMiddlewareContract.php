<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Contracts;

use SergiX44\Nutgram\Nutgram;

interface TelegramMiddlewareContract
{
    public function __invoke(Nutgram $bot, callable $next): void;
}