<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Enums;

enum WordReviewAnswerResult
{
    case CORRECT;
    case WRONG;
}