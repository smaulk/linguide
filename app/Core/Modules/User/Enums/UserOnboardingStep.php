<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Enums;

enum UserOnboardingStep
{
    case ASK_LEVEL;
    case ASK_TIMEZONE;
    case COMPLETED;
}