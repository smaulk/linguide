<?php
declare(strict_types=1);

namespace App\Core\Common\Base;

use App\Core\Common\Concerns\Taskable;

abstract class Action
{
    use Taskable;
}