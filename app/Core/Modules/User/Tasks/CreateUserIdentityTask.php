<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Tasks;

use App\Core\Common\Base\Task;
use App\Core\Modules\User\Dto\UserIdentityDto;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;
use App\Core\Modules\User\Models\UserIdentity;
use App\Core\Modules\User\Validators\UserIdentityValidator;
use Throwable;

final class CreateUserIdentityTask extends Task
{
    public function __construct(
        private readonly UserIdentityValidator $validator,
    ){}

    /**
     * @throws Throwable
     * @throws InvalidUserDataException
     */
    public function run(int $userId, UserIdentityDto $dto): UserIdentity
    {
        $this->validator->validate($dto);

        $identity = new UserIdentity();
        $identity->user_id = $userId;
        $identity->provider = $dto->provider;
        $identity->provider_user_id = $dto->providerUserId;
        $identity->email = $dto->email;
        $identity->password = $dto->password;
        $identity->saveOrFail();

        return $identity;
    }
}