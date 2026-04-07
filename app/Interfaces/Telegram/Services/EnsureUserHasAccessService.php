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

    /**
     * @return bool пропустить ли запрос дальше
     */
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
        // Если защита отключена, активируем и пропускаем дальше
        if (!$this->accessManager->isEnabledAccessControl()) {
            return $this->activateUserAction->run($appUserId);
        }

        $message = $bot->message();
        // Если пользователь не вввел верный код
        if ($message === null
            || $message->text === null
            || !$this->isCorrectAccessCode($message->text)
        ) {
            $bot->sendMessage('Доступ закрыт! Введите код активации.');
            return false;
        }

        // Если пользователь ввел верный код, активируем его
        if ($this->activateUserAction->run($appUserId)) {
            $bot->sendMessage('Успешная активация!');
        }
        // Удаляем сообщение с кодом
        $message->delete();

        // Не пропускаем запрос дальше, т.к. пользователь вводил код
        return false;
    }

    private function isCorrectAccessCode(string $message): bool
    {
        $text = trim($message);
        return $text !== '' && $this->accessManager->isCorrectAccessCode($text);
    }

    private function handleBlocked(Nutgram $bot): false
    {
        $bot->sendMessage('Доступ закрыт! Вы заблокированы.');
        return false;
    }
}