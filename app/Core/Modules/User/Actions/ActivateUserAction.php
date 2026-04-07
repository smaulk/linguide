<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\User\Enums\UserStatus;
use App\Core\Modules\User\Models\User;

final class ActivateUserAction extends Action
{
    /**
     * @param int $userId
     * @return bool успешная активация
     */
    public function run(int $userId): bool
    {
        return (bool)User::query()
            ->where('id', $userId)
            ->update(['status' => UserStatus::ACTIVE]);
    }
}