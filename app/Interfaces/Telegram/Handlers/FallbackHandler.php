<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

final class FallbackHandler extends Handler
{
    protected function handle(Nutgram $bot, ...$parameters): ?Message
    {
        return $bot->sendMessage('Извините, я вас не понимаю.');
    }
}