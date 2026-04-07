<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Services;

use App\Core\Modules\User\Actions\FindUserByIdentityAction;
use App\Core\Modules\User\Actions\RegisterUserAction;
use App\Core\Modules\User\Dto\FindUserByIdentityDto;
use App\Core\Modules\User\Dto\RegisterUserDto;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Enums\UserStatus;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;
use App\Interfaces\Telegram\Parents\Service;
use Illuminate\Support\Facades\Log;
use LogicException;
use SergiX44\Nutgram\Telegram\Types\User\User as TelegramUser;
use Throwable;

final class ResolveUserService extends Service
{
    public function __construct(
        private readonly FindUserByIdentityAction $findUserAction,
        private readonly RegisterUserAction $createUserAction,
    ){}

    public function run(TelegramUser $tgUser): ?UserDto
    {
        $tgUserId = (string)$tgUser->id;

        // Ищем пользователя
        try {
            $userDto = $this->findUser($tgUserId);
        } catch (Throwable $e) {
            $this->logException($e, $tgUserId);
            return null;
        }

        if ($userDto !== null) {
            return $userDto;
        }

        // Создаем пользователя
        try {
            return $this->createUser($tgUser, $tgUserId);
        } catch (Throwable $e) {
            $this->logException($e, $tgUserId);
            return null;
        }
    }

    /**
     * @throws LogicException
     */
    private function findUser(string $tgUserId): ?UserDto
    {
        return $this->findUserAction->run(
            FindUserByIdentityDto::fromTelegram($tgUserId),
        );
    }

    /**
     * @throws Throwable
     * @throws InvalidUserDataException
     * @throws LogicException
     */
    private function createUser(TelegramUser $tgUser, string $tgUserId): UserDto
    {
        $userName = trim($tgUser->first_name);

        return $this->createUserAction->run(
            RegisterUserDto::fromTelegram(
                name: $userName,
                status: UserStatus::INACTIVE,
                tgUserId: $tgUserId
            ),
        );
    }

    private function logException(Throwable $e, string $tgUserId): void
    {
        Log::warning('Telegram user resolve failed', [
            'tg_user_id' => $tgUserId,
            'exception'  => $e::class,
            'message'    => $e->getMessage(),
        ]);
    }
}