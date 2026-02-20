<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Interfaces\Telegram\Parents\Handler;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Message\Message;
use Throwable;

final class ExceptionHandler extends Handler
{
    public function __invoke(Nutgram $bot, Throwable $exception): void
    {
        Log::warning('Unknown handler exception', [
            'tg_user_id'  => $bot->userId(),
            'app_user_id' => $this->getAppUserId($bot),
            'exception'   => $exception::class,
            'message'     => $exception->getMessage(),
        ]);

        $bot->sendMessage('Произошла неизвестная ошибка :(');
    }
}