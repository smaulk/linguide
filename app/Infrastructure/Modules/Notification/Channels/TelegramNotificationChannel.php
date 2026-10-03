<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Notification\Channels;

use App\Core\Modules\Notification\Contracts\NotificationChannelContract;
use App\Core\Modules\Notification\Dto\NotificationDto;
use App\Core\Modules\Notification\Dto\NotificationRecipientDto;
use App\Core\Modules\User\Enums\UserProviderType;
use SergiX44\Nutgram\Nutgram;

final readonly class TelegramNotificationChannel implements NotificationChannelContract
{
    public function __construct(private Nutgram $bot){}

    public function provider(): UserProviderType
    {
        return UserProviderType::TELEGRAM;
    }

    public function send(NotificationRecipientDto $recipient, NotificationDto $notification): void
    {
        $this->bot->sendMessage(text: $notification->text, chat_id: $recipient->address);
    }
}
