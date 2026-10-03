<?php
declare(strict_types=1);

namespace App\Core\Modules\Notification\Jobs;

use App\Core\Common\Parents\Job;
use App\Core\Modules\Notification\Actions\NotifyUserAction;
use App\Core\Modules\Notification\Dto\NotificationDto;

class NotifyUserJob extends Job
{
    public function __construct(
        public int $userId,
        public NotificationDto $notification,
    ){}

    public function handle(NotifyUserAction $action): void
    {
        $action->run($this->userId, $this->notification);
    }
}