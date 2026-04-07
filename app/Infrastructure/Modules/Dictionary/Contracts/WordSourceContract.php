<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Dictionary\Contracts;

use App\Core\Modules\Dictionary\Dto\WordImportDto;
use App\Infrastructure\Common\Exceptions\MissingResourceException;

interface WordSourceContract
{
    /**
     * @param string $name имя ресурса
     * @return iterable<WordImportDto>
     * @throws MissingResourceException
     */
    public function get(string $name): iterable;
}