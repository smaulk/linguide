<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Interfaces\Telegram\Keyboards\Reply\MainMenuReplyKeyboard;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class StartHandler extends Handler
{
    public function __construct(private readonly MainMenuReplyKeyboard $keyboard) {}

    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: $this->getText(),
            reply_markup: $this->keyboard->make(),
        );
    }

    private function getText(): string
    {
        return 'Добро пожаловать в Linguide - помощник для изучения английского языка!';
    }
}