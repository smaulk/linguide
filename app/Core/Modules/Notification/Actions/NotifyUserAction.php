<?php
declare(strict_types=1);

namespace App\Core\Modules\Notification\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Notification\Dto\NotificationDto;
use App\Core\Modules\Term\Tasks\NotifyUserTask;
use App\Core\Modules\User\Models\User;

final class NotifyUserAction extends Action
{
    public function __construct(private readonly NotifyUserTask $notifyTask){}

    public function run(int $userId, NotificationDto $notification): void
    {
        $user = User::query()
            ->select(['id'])
            ->with([
                'identities:id,user_id,provider,provider_user_id,email',
            ])
            ->find($userId);

        if ($user === null) {
            return;
        }

        $this->notifyTask->run($user, $notification);
    }
}
