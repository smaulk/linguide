<?php

namespace App\Interfaces\Console\Commands;

use App\Interfaces\Telegram\Classes\TelegramWebhookHelper;
use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:webhook:set';

    protected $description = 'Устанавливает webhook для телеграм бота';

    public function handle(Nutgram $bot): int
    {
        $url = TelegramWebhookHelper::url();
        $bot->setWebhook(
            url: $url,
            secret_token: TelegramWebhookHelper::isSafeMode() ? TelegramWebhookHelper::secretToken() : null,
        );

        $this->info("Вебхук был установлен на url: $url");

        return self::SUCCESS;
    }
}
