<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Notification\Contracts\NotifierContract;
use App\Core\Modules\Notification\Dto\NotificationDto;
use App\Core\Modules\Notification\Dto\NotificationRecipientDto;
use App\Core\Modules\User\Models\User;

final class NotifyUserTask extends Task
{
    public function __construct(private readonly NotifierContract $notifier){}

    public function run(User $user, NotificationDto $notification): void
    {
        $recipients = [];

        foreach ($user->identities as $identity) {
            $address = $identity->provider_user_id ?? $identity->email;

            if ($address !== null && $address !== '') {
                $recipients[] = new NotificationRecipientDto($identity->provider, $address);
            }
        }

        $this->notifier->notify($recipients, $notification);
    }
}