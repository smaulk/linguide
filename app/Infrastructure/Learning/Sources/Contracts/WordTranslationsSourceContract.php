<?php
declare(strict_types=1);

namespace App\Infrastructure\Learning\Sources\Contracts;

use App\Core\Modules\Words\Dto\WordDatasetDto;
use App\Infrastructure\Support\Exceptions\MissingResourceException;

interface WordTranslationsSourceContract
{
    /**
     * @param string $name имя ресурса
     * @return iterable<WordDatasetDto>
     * @throws MissingResourceException
     */
    public function get(string $name): iterable;
}