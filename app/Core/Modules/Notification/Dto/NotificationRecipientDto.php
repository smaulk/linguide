<?php
declare(strict_types=1);

namespace App\Core\Modules\Notification\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\User\Enums\UserProviderType;

final readonly class NotificationRecipientDto extends Dto
{
    public function __construct(
        public UserProviderType $provider,
        public string $address,
    ) {}
}