<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Enums;

use App\Core\Common\Concerns\BaseEnum;

enum UserStatus: string
{
    use BaseEnum;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BLOCKED = 'blocked';
}