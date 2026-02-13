<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers\Conversations;

use App\Core\Common\Concerns\Actionable;
use App\Interfaces\Telegram\Concerns\InteractsWithAppUser;
use App\Interfaces\Telegram\Contracts\ConversationContract;
use SergiX44\Nutgram\Conversations\Conversation as NutgramConversation;
use SergiX44\Nutgram\Nutgram;

abstract class Conversation extends NutgramConversation implements ConversationContract
{
    use Actionable, InteractsWithAppUser;

    protected function beforeStep(Nutgram $bot): void
    {
        $this->setAppUserId($bot);
    }
}