<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Validators;

use App\Core\Common\Base\Validator;
use App\Core\Modules\User\Dto\UserIdentityDto;
use App\Core\Modules\User\Enums\UserProviderType;
use App\Core\Modules\User\Exceptions\InvalidUserDataException;

final class UserIdentityValidator extends Validator
{
    /**
     * @throws InvalidUserDataException
     */
    public function validate(UserIdentityDto $dto): void
    {
        if ($dto->provider === UserProviderType::EMAIL) {
            if ($dto->email === null || $dto->password === null) {
                throw new InvalidUserDataException('Почта или пароль не могут быть пустыми');
            }
        } else if ($dto->providerUserId === null) {
            throw new InvalidUserDataException('Идентификатор пользователя провайдера не может быть пустым');
        }
    }
}