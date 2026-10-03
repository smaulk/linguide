<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Keyboards\Inline\SelectionUserRemindersInlineKeyboard;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class ShowUserRemindersHandler extends Handler
{
    public function __construct(
        private readonly AppUserContext $userContext,
        private readonly SelectionUserRemindersInlineKeyboard $keyboard
    ){}

    public function __invoke(Nutgram $bot): void
    {
        $appUser = $this->userContext->get($bot);
        $settings = $appUser->settings;

        $bot->sendMessage(
            text: $this->getText($settings->remindersEnabled),
            reply_markup: $this->keyboard->make(),
        );
    }

    private function getText(bool $remindersEnabled): string
    {
        $status = $remindersEnabled ? 'включены' : 'выключены';
        return 'Напоминания о повторении: ' . $status;
    }
}