<?php
declare(strict_types=1);

namespace App\Infrastructure\Learning\Sources\Contracts;

use App\Core\Modules\Words\Dto\WordDto;
use App\Infrastructure\Support\Exceptions\MissingResourceException;

interface WordTranslationsSourceContract
{
    /**
     * @param string $name имя ресурса
     * @return iterable<WordDto>
     * @throws MissingResourceException
     */
    public function get(string $name): iterable;
}