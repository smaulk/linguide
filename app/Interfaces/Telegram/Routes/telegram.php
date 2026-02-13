<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Interfaces\Telegram\Handlers\AskAiHandler;
use App\Interfaces\Telegram\Handlers\Conversations\TestConversation;
use App\Interfaces\Telegram\Handlers\FallbackHandler;
use App\Interfaces\Telegram\Handlers\ExceptionHandler;
use App\Interfaces\Telegram\Handlers\StartHandler;

$bot->onException(ExceptionHandler::class);
$bot->fallback(FallbackHandler::class);

$bot->onCommand('start', StartHandler::class)
    ->description('Start using the bot');

// $bot->onText('Диалог', TestConversation::class);

$bot->onCommand('ask {message}', AskAiHandler::class)
    ->description('Send a message to AI');


$bot->registerMyCommands();