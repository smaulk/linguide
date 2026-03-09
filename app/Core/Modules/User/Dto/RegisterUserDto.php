<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\User\Enums\UserProviderType;

final readonly class RegisterUserDto extends Dto
{
    private function __construct(
        public string $name,
        public EmailUserIdentityDto|ExternalUserIdentityDto $identity,
        public ?LanguageLevel $languageLevel = null,
    ){}

    public static function fromEmail(string $name, string $email, string $password,
        ?LanguageLevel $languageLevel = null
    ): self
    {
        return new self(
            name: $name,
            identity: new EmailUserIdentityDto($email, $password),
            languageLevel: $languageLevel
        );
    }

    public static function fromTelegram(string $name, string $tgUserId,
        ?LanguageLevel $languageLevel = null
    ): self
    {
        return new self(
            name: $name,
            identity: new ExternalUserIdentityDto(UserProviderType::TELEGRAM, $tgUserId),
            languageLevel: $languageLevel
        );
    }
}