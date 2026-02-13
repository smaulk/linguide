<?php
declare(strict_types=1);

use App\Interfaces\Telegram\Classes\TelegramWebhookHelper;
use SergiX44\Nutgram\Nutgram;

Route::post(TelegramWebhookHelper::routePath(), fn(Nutgram $bot) => $bot->run());
