<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Middlewares;

use App\Core\Common\Concerns\Actionable;
use SergiX44\Nutgram\Nutgram;

abstract class TelegramMiddleware
{
    use Actionable;

    abstract public function __invoke(Nutgram $bot, callable $next): void;
}