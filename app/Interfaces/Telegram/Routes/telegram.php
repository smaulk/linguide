<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Interfaces\Telegram\Conversations\TestConversation;
use App\Interfaces\Telegram\Handlers\AskAiHandler;
use App\Interfaces\Telegram\Handlers\StartHandler;

$bot->onCommand('start', StartHandler::class)
    ->description('Start using the bot');


$bot->onCommand('ask {message}', AskAiHandler::class)
    ->description('Send a message to AI');



$bot->registerMyCommands();