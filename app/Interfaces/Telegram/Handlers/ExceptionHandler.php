<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Parents\Handler;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use Throwable;

final class ExceptionHandler extends Handler
{
    public function __construct(private readonly AppUserContext $appUserContext){}

    public function __invoke(Nutgram $bot, Throwable $exception): void
    {
        Log::warning('Unknown handler exception', [
            'tg_user_id'  => $bot->userId(),
            'app_user_id' => $this->appUserContext->get($bot)->id,
            'exception'   => $exception::class,
            'message'     => $exception->getMessage(),
        ]);

        $bot->sendMessage('Произошла неизвестная ошибка :(');
    }
}