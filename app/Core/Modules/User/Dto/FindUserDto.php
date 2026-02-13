<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Dto;

use App\Core\Common\Base\Dto;
use App\Core\Modules\User\Enums\UserProviderType;

final readonly class FindUserDto extends Dto
{
    private function __construct(
        public UserProviderType $providerType,
        public ?string $email = null,
        public ?string $providerUserId = null,
    ){}

    public static function fromTelegram(string $tgUserId): self
    {
        return new self(
            providerType: UserProviderType::TELEGRAM,
            providerUserId: $tgUserId,
        );
    }

    public static function fromEmail(string $email): self
    {
        return new self(
            providerType: UserProviderType::EMAIL,
            email: $email,
        );
    }
}