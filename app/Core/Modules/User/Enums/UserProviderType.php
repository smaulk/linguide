<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Enums;

use App\Core\Common\Concerns\BaseEnum;

enum UserProviderType: string
{
    use BaseEnum;

    case EMAIL = 'email';
    case TELEGRAM = 'telegram';
}