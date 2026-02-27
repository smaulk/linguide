<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class FallbackHandler extends Handler
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage('Извините, я вас не понимаю.');
    }
}