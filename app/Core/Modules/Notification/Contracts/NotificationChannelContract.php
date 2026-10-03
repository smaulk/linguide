<?php
declare(strict_types=1);

namespace App\Core\Modules\Notification\Contracts;

use App\Core\Modules\Notification\Dto\NotificationDto;
use App\Core\Modules\Notification\Dto\NotificationRecipientDto;
use App\Core\Modules\User\Enums\UserProviderType;

interface NotificationChannelContract
{
    public function provider(): UserProviderType;

    public function send(NotificationRecipientDto $recipient, NotificationDto $notification): void;
}
