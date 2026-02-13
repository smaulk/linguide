<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Middlewares;

use App\Core\Modules\User\Actions\CreateUserAction;
use App\Core\Modules\User\Actions\FindUserAction;
use App\Core\Modules\User\Dto\CreateUserDto;
use App\Core\Modules\User\Dto\FindUserDto;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;
use App\Core\Modules\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use Throwable;

final class ResolveTelegramUser extends TelegramMiddleware
{
    public function __invoke(Nutgram $bot, callable $next): void
    {
        $tgUserId = $bot->userId();
        if ($tgUserId === null) {
            return;
        }

        $userDto = $this->resolveUser($bot, (string)$tgUserId);
        if ($userDto === null) {
            return;
        }

        $bot->set('appUserId', $userDto->id);
        $next($bot);
    }

    private function resolveUser(Nutgram $bot, string $tgUserId): ?UserDto
    {
        // Ищем пользователя
        $userDto = $this->findUser($tgUserId);
        if ($userDto !== null) {
            return $userDto;
        }

        // Создаем пользователя (телеграму нельзя отправлять ошибку)
        try {
            return $this->createUser($bot, $tgUserId);
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
        return $this->action(FindUserAction::class)->run(
            FindUserDto::fromTelegram($tgUserId),
        );
    }

    /**
     * @throws Throwable
     * @throws InvalidUserDataException
     */
    private function createUser(Nutgram $bot, string $tgUserId): UserDto
    {
        return $this->action(CreateUserAction::class)->run(
            CreateUserDto::fromTelegram($this->resolveUserName($bot), $tgUserId),
        );
    }

    private function resolveUserName(Nutgram $bot): string
    {
        $u = $bot->user();
        return trim($u?->first_name ?? $u?->username ?? 'Unknown');
    }

    private function logException(Throwable $e, string $tgUserId): void
    {
        Log::warning('Telegram user resolve failed', [
            'tg_user_id' => $tgUserId,
            'exception' => $e::class,
            'message' => $e->getMessage(),
        ]);
    }
}