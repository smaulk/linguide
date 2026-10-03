<?php
declare(strict_types=1);

namespace App\Core\Modules\Notification\Contracts;

use App\Core\Modules\Notification\Dto\NotificationDto;
use App\Core\Modules\Notification\Dto\NotificationRecipientDto;

interface NotifierContract
{
    /** @param list<NotificationRecipientDto> $recipients */
    public function notify(array $recipients, NotificationDto $notification): void;
}
