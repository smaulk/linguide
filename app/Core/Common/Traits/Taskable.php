<?php
declare(strict_types=1);

namespace App\Core\Common\Traits;

use App\Core\Common\Base\Task;

trait Taskable
{
    /**
     * @template T of Task
     * @param class-string<T> $abstract
     * @return T
     */
    public function task(string $abstract): Task
    {
        return app($abstract);
    }
}