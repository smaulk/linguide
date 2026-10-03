<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Notification\Dto\NotificationDto;
use App\Core\Modules\Notification\Jobs\NotifyUserJob;
use App\Core\Modules\Term\Tasks\GetUsersForReviewReminderTask;

final class SendReviewRemindersAction extends Action
{
    private const int REMINDER_START_HOUR = 12;

    public function __construct(private readonly GetUsersForReviewReminderTask $getUsersTask){}

    public function run(): void
    {
        $notification = new NotificationDto(
            'Вы ещё не повторяли слова сегодня. Самое время немного позаниматься!'
        );

        foreach ($this->getUsersTask->run(self::REMINDER_START_HOUR) as $userId) {
            NotifyUserJob::dispatch($userId, $notification);
        }
    }
}