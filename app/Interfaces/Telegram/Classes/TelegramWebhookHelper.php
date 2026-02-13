<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Classes;

class TelegramWebhookHelper
{
    public static function routePath(): string
    {
        $base = trim((string) config('nutgram.webhook_path'), '/');
        $secret = config('nutgram.webhook_secret_path');

        return is_null($secret) ? $base : $base.'/'.$secret;
    }

    public static function url(): string
    {
        $appUrl = rtrim((string) config('app.url'), '/');

        return $appUrl.'/webhooks/'.self::routePath();
    }

    public static function isSafeMode(): bool
    {
        return (bool) config('nutgram.safe_mode', false);
    }

    public static function secretToken(): ?string
    {
        return md5(config('app.key'));
    }
}