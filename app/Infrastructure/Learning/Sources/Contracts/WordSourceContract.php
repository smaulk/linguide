<?php
declare(strict_types=1);

namespace App\Infrastructure\Learning\Sources\Contracts;

use App\Core\Modules\Words\Dto\RawWordDto;
use App\Infrastructure\Support\Exceptions\MissingResourceException;

interface WordSourceContract
{
    /**
     * @param string $name имя ресурса
     * @return iterable<RawWordDto>
     * @throws MissingResourceException
     */
    public function get(string $name): iterable;
}