<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Enums;

enum UserOnboardingStatus
{
    case SELECT_LEVEL;
    case SELECT_TIMEZONE;
    case SUCCESS;
}