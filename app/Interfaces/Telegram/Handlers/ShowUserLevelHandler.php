<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Enums\LanguageLevel;
use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Keyboards\Inline\ShowUserLevelInlineKeyboard;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class ShowUserLevelHandler extends Handler
{
    public function __construct(
        private readonly AppUserContext $userContext,
        private readonly ShowUserLevelInlineKeyboard $keyboard
    ){}

    public function __invoke(Nutgram $bot): void
    {
        $appUser = $this->userContext->get($bot);
        $settings = $appUser->settings;

        $bot->sendMessage(
            text: $this->getText($settings->level),
            reply_markup: $this->keyboard->make(),
        );
    }

    private function getText(?LanguageLevel $level): string
    {
        return $level !== null
            ? "Ваш текущий уровень: $level->name."
            : 'У вас не установлен уровень!';
    }
}