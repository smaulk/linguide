<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Enums;

use App\Core\Common\Concerns\BaseEnum;

enum TermCandidateStatus: string
{
    use BaseEnum;

    case PENDING = 'pending';
    case VALID   = 'valid';
    case INVALID = 'invalid';
}