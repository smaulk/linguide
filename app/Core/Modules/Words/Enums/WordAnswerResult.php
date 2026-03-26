<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Enums;

enum WordAnswerResult
{
    case CORRECT;
    case WRONG;
}