<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Vo\UtcOffset;
use App\Interfaces\Telegram\Keyboards\Inline\ShowUserTimezoneInlineKeyboard;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class ShowUserTimezoneHandler extends Handler
{
    public function __construct(private readonly ShowUserTimezoneInlineKeyboard $keyboard){}


    public function __invoke(Nutgram $bot): void
    {
        $appUser = $this->getAppUser($bot);
        $settings = $appUser->settings;

        $bot->sendMessage(
            text: $this->getText($settings->utcOffset),
            reply_markup: $this->keyboard->make(),
        );
    }

    private function getText(?UtcOffset $offset): string
    {
        return $offset !== null
            ? "Ваш текущий часовой пояс: $offset."
            : 'У вас не установлен часовой пояс!';
    }
}