<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Enums;

enum ReviewAnswerResult
{
    case CORRECT;
    case WRONG;
}