<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Enums;

use App\Core\Common\Concerns\BaseEnum;

enum TermType: string
{
    use BaseEnum;

    case WORD   = 'word';
    case PHRASE = 'phrase';
}
