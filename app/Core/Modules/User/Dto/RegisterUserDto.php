<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\User\Enums\UserProviderType;
use App\Core\Modules\User\Enums\UserStatus;

final readonly class RegisterUserDto extends Dto
{
    private function __construct(
        public string $name,
        public UserStatus $status,
        public EmailUserIdentityDto|ExternalUserIdentityDto $identity,
    ){}

    public static function fromEmail(string $name, UserStatus $status, string $email, string $password): self
    {
        return new self(
            name: $name,
            status: $status,
            identity: new EmailUserIdentityDto($email, $password),
        );
    }

    public static function fromTelegram(string $name, UserStatus $status, string $tgUserId): self
    {
        return new self(
            name: $name,
            status: $status,
            identity: new ExternalUserIdentityDto(UserProviderType::TELEGRAM, $tgUserId),
        );
    }
}