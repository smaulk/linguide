<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Dto;

use App\Core\Common\Base\Dto;
use App\Core\Modules\User\Enums\UserProviderType;

final readonly class UserIdentityDto extends Dto
{
    private function __construct(
        public UserProviderType $provider,
        public ?string $providerUserId = null,
        public ?string $email = null,
        public ?string $password = null,
    ){}

    public static function fromEmail(string $email, string $password): self
    {
        return new self(
            provider: UserProviderType::EMAIL,
            email: $email,
            password: $password,
        );
    }

    public static function fromTelegram(string $tgUserId): self
    {
        return new self(
            provider: UserProviderType::TELEGRAM,
            providerUserId: $tgUserId,
        );
    }
}