<?php
declare(strict_types=1);

namespace App\Core\Common\Traits;

use App\Core\Common\Base\Action;

trait Actionable
{
    /**
     * @template T of Action
     * @param class-string<T> $abstract
     * @return T
     */
    public function action(string $abstract): Action
    {
        return app($abstract);
    }
}