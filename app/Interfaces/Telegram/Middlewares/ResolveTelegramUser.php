<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Middlewares;

use App\Core\Modules\User\Actions\CreateUserAction;
use App\Core\Modules\User\Actions\FindUserAction;
use App\Core\Modules\User\Dto\CreateUserDto;
use App\Core\Modules\User\Dto\FindUserDto;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;
use App\Interfaces\Telegram\Parents\TelegramMiddleware;
use Psr\Log\LoggerInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\User\User as TelegramUser;
use Throwable;

final class ResolveTelegramUser extends TelegramMiddleware
{
    public function __construct(
        private readonly FindUserAction $findUserAction,
        private readonly CreateUserAction $createUserAction,
        private readonly LoggerInterface $logger,
    ){}

    public function __invoke(Nutgram $bot, callable $next): void
    {
        $tgUser = $bot->user();
        if ($tgUser === null) {
            return;
        }
        $tgUserId = (string)$tgUser->id;

        $userDto = $this->resolveUser($tgUser, $tgUserId);
        if ($userDto === null) {
            return;
        }

        $bot->set('appUserId', $userDto->id);
        $next($bot);
    }

    private function resolveUser(TelegramUser $tgUser, string $tgUserId): ?UserDto
    {
        // Ищем пользователя
        $userDto = $this->findUser($tgUserId);
        if ($userDto !== null) {
            return $userDto;
        }

        // Создаем пользователя (в телеграм нельзя отвечать ошибкой)
        try {
            return $this->createUser($tgUser, $tgUserId);
        } catch (InvalidUserDataException $e) {
            $this->logException($e, $tgUserId);
            return null;
        } catch (Throwable $e) {
            $this->logException($e, $tgUserId);
            return $this->findUser($tgUserId);
        }
    }

    private function findUser(string $tgUserId): ?UserDto
    {
        return $this->findUserAction->run(
            FindUserDto::fromTelegram($tgUserId),
        );
    }

    /**
     * @throws Throwable
     * @throws InvalidUserDataException
     */
    private function createUser(TelegramUser $tgUser, string $tgUserId): UserDto
    {
        return $this->createUserAction->run(
            CreateUserDto::fromTelegram(trim($tgUser->first_name), $tgUserId),
        );
    }

    private function logException(Throwable $e, string $tgUserId): void
    {
        $this->logger->warning('Telegram user resolve failed', [
            'tg_user_id' => $tgUserId,
            'exception'  => $e::class,
            'message'    => $e->getMessage(),
        ]);
    }
}