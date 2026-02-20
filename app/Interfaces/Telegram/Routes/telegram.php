<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Interfaces\Telegram\Commands\BaseCommand;
use App\Interfaces\Telegram\Commands\MainMenuCommand;
use App\Interfaces\Telegram\Handlers\AskAiHandler;
use App\Interfaces\Telegram\Handlers\Conversations\TalkConversation;
use App\Interfaces\Telegram\Handlers\FallbackHandler;
use App\Interfaces\Telegram\Handlers\ExceptionHandler;
use App\Interfaces\Telegram\Handlers\StartHandler;

$bot->onException(ExceptionHandler::class);
$bot->fallback(FallbackHandler::class);

$bot->onCommand(BaseCommand::START->value, StartHandler::class)
    ->description('Start using the bot');

$bot->onText(MainMenuCommand::START_TALK->value, TalkConversation::class);
$bot->onCommand('ask {message}', AskAiHandler::class)
    ->description('Ask the ai');

$bot->registerMyCommands();