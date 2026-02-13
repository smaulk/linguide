<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;


use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

final class ExceptionHandler extends Handler
{
    protected function handle(Nutgram $bot, ...$parameters): ?Message
    {
        [$exception] = $parameters;
        Log::warning('Unknown handler exception', [
            'tg_user_id'  => $bot->userId(),
            'app_user_id' => $this->getAppUserId(),
            'exception'   => $exception::class,
            'message'     => $exception->getMessage(),
        ]);

        return $bot->sendMessage('Произошла неизвестная ошибка :(');
    }
}