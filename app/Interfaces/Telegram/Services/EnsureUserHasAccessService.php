<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Services;

use App\Core\Modules\App\Classes\AccessManager;
use App\Core\Modules\User\Actions\ActivateUserAction;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Enums\UserStatus;
use App\Interfaces\Telegram\Parents\Service;
use SergiX44\Nutgram\Nutgram;

final class EnsureUserHasAccessService extends Service
{
    public function __construct(
        private readonly AccessManager $accessManager,
        private readonly ActivateUserAction $activateUserAction,
    ){}

    public function run(Nutgram $bot, UserDto $appUser): bool
    {
        return match ($appUser->status) {
            UserStatus::ACTIVE   => true,
            UserStatus::INACTIVE => $this->handleInactive($bot, $appUser->id),
            UserStatus::BLOCKED  => $this->handleBlocked($bot),
        };
    }

    private function handleInactive(Nutgram $bot, int $appUserId): bool
    {
        if (!$this->accessManager->isEnabledAccessControl()) {
            $this->activateUserAction->run($appUserId);
            return true;
        }

        if (!$this->isCorrectAccessCode($bot)) {
            $bot->sendMessage('Доступ закрыт! Введите код активации.');
            return false;
        }

        $this->activateUserAction->run($appUserId);

        return true;
    }

    private function isCorrectAccessCode(Nutgram $bot): bool
    {
        $message = trim($bot->message()->text ?? '');

        return $message !== '' && $this->accessManager->isCorrectAccessCode($message);
    }

    private function handleBlocked(Nutgram $bot): false
    {
        $bot->sendMessage('Доступ закрыт! Вы заблокированы.');
        return false;
    }
}