<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Notification\Notifiers;

use App\Core\Modules\Notification\Contracts\NotificationChannelContract;
use App\Core\Modules\Notification\Contracts\NotifierContract;
use App\Core\Modules\Notification\Dto\NotificationDto;

final readonly class NotificationManager implements NotifierContract
{
    /** @param iterable<NotificationChannelContract> $channels */
    public function __construct(private iterable $channels){}

    public function notify(array $recipients, NotificationDto $notification): void
    {
        foreach ($recipients as $recipient) {
            foreach ($this->channels as $channel) {
                if ($channel->provider() === $recipient->provider) {
                    $channel->send($recipient, $notification);
                    break;
                }
            }
        }
    }
}
