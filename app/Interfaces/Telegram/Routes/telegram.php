<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Interfaces\Telegram\Commands\BaseCommand;
use App\Interfaces\Telegram\Commands\MainMenuCommand;
use App\Interfaces\Telegram\Conversations\ReviewConversation;
use App\Interfaces\Telegram\Conversations\TalkConversation;
use App\Interfaces\Telegram\Handlers\ExceptionHandler;
use App\Interfaces\Telegram\Handlers\FallbackHandler;
use App\Interfaces\Telegram\Handlers\ShowUserLevelHandler;
use App\Interfaces\Telegram\Handlers\ShowUserTimezoneHandler;
use App\Interfaces\Telegram\Handlers\ShowUserReviewLimitHandler;
use App\Interfaces\Telegram\Handlers\MainMenuHandler;
use App\Interfaces\Telegram\Handlers\SettingsMenuHandler;
use App\Interfaces\Telegram\Handlers\SetUserLevelHandler;
use App\Interfaces\Telegram\Handlers\SetUserTimezoneHandler;
use App\Interfaces\Telegram\Handlers\SetUserReviewLimitHandler;
use App\Interfaces\Telegram\Handlers\StartHandler;
use App\Interfaces\Telegram\Handlers\SelectionUserLevelHandler;
use App\Interfaces\Telegram\Handlers\SelectionUserTimezoneHandler;
use App\Interfaces\Telegram\Handlers\SelectionUserReviewLimitHandler;
use \App\Interfaces\Telegram\Commands\SettingsMenuCommand;

$bot->onException(ExceptionHandler::class);
$bot->fallback(FallbackHandler::class);

$bot->onCommand(BaseCommand::START->value, StartHandler::class)
    ->description('Start using the bot');

$bot->onText(BaseCommand::MAIN_MENU->value, MainMenuHandler::class);

// region Main menu
$bot->onText(MainMenuCommand::SETTINGS->value, SettingsMenuHandler::class);
$bot->onText(MainMenuCommand::START_TALK->value, TalkConversation::class);
$bot->onText(MainMenuCommand::REVIEW->value, ReviewConversation::class);
// endregion

// region Settings menu

// SHOW
$bot->onText(SettingsMenuCommand::LEVEL->value, ShowUserLevelHandler::class);
$bot->onText(SettingsMenuCommand::TIMEZONE->value, ShowUserTimezoneHandler::class);
$bot->onText(SettingsMenuCommand::REVIEW_LIMIT->value, ShowUserReviewLimitHandler::class);

// SELECT
$bot->onCallbackQueryData(
    SettingsMenuCommand::SELECT_LEVEL_CALLBACK->value,
    SelectionUserLevelHandler::class
);
$bot->onCallbackQueryData(
    SettingsMenuCommand::SELECT_TIMEZONE_CALLBACK->value,
    SelectionUserTimezoneHandler::class
);
$bot->onCallbackQueryData(
    SettingsMenuCommand::SELECT_REVIEW_LIMIT_CALLBACK->value,
    SelectionUserReviewLimitHandler::class
);

// SET
$bot->onCallbackQueryData(
    SettingsMenuCommand::SET_LEVEL_CALLBACK->value . '{level}',
    SetUserLevelHandler::class
);
$bot->onCallbackQueryData(
    SettingsMenuCommand::SET_TIMEZONE_CALLBACK->value . '{offset}',
    SetUserTimezoneHandler::class,
);
$bot->onCallbackQueryData(
    SettingsMenuCommand::SET_REVIEW_LIMIT_CALLBACK->value . '{limit}',
    SetUserReviewLimitHandler::class,
);
// endregion

$bot->registerMyCommands();