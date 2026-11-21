<?php

declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Reply;

use App\Core\Modules\Term\Enums\ReviewStatisticsPeriod;
use App\Interfaces\Telegram\Commands\BaseCommand;
use App\Interfaces\Telegram\Parents\ReplyKeyboard;
use SergiX44\Nutgram\Telegram\Properties\ButtonStyle;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;

final class ReviewStatisticsPeriodReplyKeyboard extends ReplyKeyboard
{
    protected function rows(): array
    {
        return [
            [
                KeyboardButton::make(text: ReviewStatisticsPeriod::DAY->label()),
                KeyboardButton::make(text: ReviewStatisticsPeriod::WEEK->label()),
            ],
            [
                KeyboardButton::make(text: ReviewStatisticsPeriod::MONTH->label()),
                KeyboardButton::make(text: ReviewStatisticsPeriod::HALF_YEAR->label()),
            ],
            [
                KeyboardButton::make(text: ReviewStatisticsPeriod::YEAR->label()),
            ],
            [
                KeyboardButton::make(text: BaseCommand::BACK_MAIN_MENU->value, style: ButtonStyle::PRIMARY),
            ],
        ];
    }
}
