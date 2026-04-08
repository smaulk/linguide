<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Dictionary\Contracts;

use App\Core\Modules\Dictionary\Dto\TermDatasetDto;
use App\Infrastructure\Common\Exceptions\MissingResourceException;

interface TermsSourceContract
{
    /**
     * @param string $name имя ресурса
     * @return iterable<TermDatasetDto>
     * @throws MissingResourceException
     */
    public function get(string $name): iterable;
}