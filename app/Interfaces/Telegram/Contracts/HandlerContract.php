<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Contracts;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

interface HandlerContract
{
    public function __invoke(Nutgram $bot, ...$parameters): ?Message;
}