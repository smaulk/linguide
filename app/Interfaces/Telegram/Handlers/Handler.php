<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Common\Concerns\Actionable;
use App\Interfaces\Telegram\Concerns\InteractsWithAppUser;
use App\Interfaces\Telegram\Contracts\HandlerContract;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

abstract class Handler implements HandlerContract
{
    use Actionable, InteractsWithAppUser;

    public function __invoke(Nutgram $bot, ...$parameters): ?Message
    {
        $this->setAppUserId($bot);
        return $this->handle($bot, ...$parameters);
    }

    abstract protected function handle(Nutgram $bot, ...$parameters): ?Message;
}