<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\User\Dto\EmailUserIdentityDto;
use App\Core\Modules\User\Dto\ExternalUserIdentityDto;
use App\Core\Modules\User\Enums\UserProviderType;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;
use App\Core\Modules\User\Models\UserIdentity;
use App\Core\Modules\User\Vo\Email;
use App\Core\Modules\User\Vo\Password;
use Throwable;

final class CreateUserIdentityTask extends Task
{
    /**
     * @throws Throwable
     * @throws InvalidUserDataException
     */
    public function run(int $userId, EmailUserIdentityDto|ExternalUserIdentityDto $dto): UserIdentity
    {
        $identity = new UserIdentity();
        $identity->user_id = $userId;

        if ($dto instanceof EmailUserIdentityDto) {
            $this->setEmailIdentity($identity, $dto);
        } else {
            $this->setExternalIdentity($identity, $dto);
        }
        $identity->saveOrFail();

        return $identity;
    }

    private function setEmailIdentity(UserIdentity $identity, EmailUserIdentityDto $dto): void
    {
        $identity->provider = UserProviderType::EMAIL;
        $identity->email = Email::fromString($dto->email)->value;
        $identity->password = Password::fromString($dto->password)->value;
    }

    private function setExternalIdentity(UserIdentity $identity, ExternalUserIdentityDto $dto): void
    {
        if ($dto->provider === UserProviderType::EMAIL) {
            throw new InvalidUserDataException('External identity cannot be EMAIL provider');
        }

        $identity->provider = $dto->provider;
        $identity->provider_user_id = $dto->providerUserId;
    }
}