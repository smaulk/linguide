<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Parents;

use App\Interfaces\Telegram\Concerns\InteractsWithAppUser;
use App\Interfaces\Telegram\Contracts\HandlerContract;

abstract class Handler implements HandlerContract
{
    use InteractsWithAppUser;
}