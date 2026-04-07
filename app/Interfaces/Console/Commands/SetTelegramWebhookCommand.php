<?php

namespace App\Interfaces\Console\Commands;

use App\Interfaces\Telegram\Classes\TelegramWebhookHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use Throwable;

final class SetTelegramWebhookCommand extends Command
{
    protected $signature = 'telegram:webhook:set';

    protected $description = 'Sets a webhook for a Telegram bot';

    public function handle(Nutgram $bot): int
    {
        $url = TelegramWebhookHelper::url();

        try {
            $bot->setWebhook(
                url: $url,
                secret_token: TelegramWebhookHelper::isSafeMode()
                    ? TelegramWebhookHelper::secretToken()
                    : null,
            );
        } catch (Throwable $e) {
            Log::error('Telegram webhook set failed.', ['exception' => $e]);
            $this->error('Failed to set Telegram webhook.');

            return self::FAILURE;
        }

        $this->info("The telegram webhook was set to url: $url.");

        return self::SUCCESS;
    }
}
