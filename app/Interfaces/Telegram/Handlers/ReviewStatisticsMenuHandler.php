<?php

declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Interfaces\Telegram\Keyboards\Reply\ReviewStatisticsPeriodReplyKeyboard;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class ReviewStatisticsMenuHandler extends Handler
{
    public function __construct(private readonly ReviewStatisticsPeriodReplyKeyboard $keyboard) {}

    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: 'Выберите период статистики:',
            reply_markup: $this->keyboard->make(),
        );
    }
}
