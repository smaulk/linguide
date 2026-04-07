<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Enums;

use App\Core\Common\Concerns\BaseEnum;

enum WordReviewSessionStatus: string
{
    use BaseEnum;

    case ACTIVE    = 'active';
    case FINISHED  = 'finished';
    case ABANDONED = 'abandoned';
}