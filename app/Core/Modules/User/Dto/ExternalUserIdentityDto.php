<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\User\Enums\UserProviderType;

final readonly class ExternalUserIdentityDto extends Dto
{
    public function __construct(
        public UserProviderType $provider,
        public string $providerUserId,
    ){}
}