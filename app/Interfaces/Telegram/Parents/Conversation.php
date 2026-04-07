<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Parents;

use App\Interfaces\Telegram\Contracts\ConversationContract;
use SergiX44\Nutgram\Conversations\Conversation as NutgramConversation;

abstract class Conversation extends NutgramConversation implements ConversationContract
{
    //
}