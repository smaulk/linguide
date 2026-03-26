<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Enums;

use App\Core\Common\Concerns\BaseEnum;

enum LanguageLevel: int
{
    use BaseEnum;

    case A1 = 1;
    case A2 = 2;
    case B1 = 3;
    case B2 = 4;
    case C1 = 5;
    case C2 = 6;
}
