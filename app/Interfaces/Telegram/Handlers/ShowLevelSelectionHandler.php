<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Interfaces\Telegram\Keyboards\Inline\LevelSelectionInlineKeyboard;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class ShowLevelSelectionHandler extends Handler
{
    public function __construct(private readonly LevelSelectionInlineKeyboard $keyboard){}

    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: $this->getText(),
            reply_markup: $this->keyboard->make(),
        );
    }

    private function getText(): string
    {
        return 'Перед началом использования, пожалуйста, выберите ваш текущий уровень знания английского языка.';
    }
}