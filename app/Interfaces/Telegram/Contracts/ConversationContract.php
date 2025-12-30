<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Contracts;

use SergiX44\Nutgram\Nutgram;

interface ConversationContract
{
    public function __invoke(Nutgram $bot, ...$parameters): mixed;

    public function start(Nutgram $bot, ...$parameters): void;
}