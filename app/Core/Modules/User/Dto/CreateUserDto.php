<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\User\Enums\UserProviderType;

final readonly class CreateUserDto extends Dto
{
    private function __construct(
        public string $name,
        public EmailUserIdentityDto|ExternalUserIdentityDto $identity,
    ){}

    public static function fromEmail(string $name, string $email, string $password): self
    {
        return new self(
            name: $name,
            identity: new EmailUserIdentityDto($email, $password),
        );
    }

    public static function fromTelegram(string $name, string $tgUserId): self
    {
        return new self(
            name: $name,
            identity: new ExternalUserIdentityDto(UserProviderType::TELEGRAM, $tgUserId),
        );
    }
}