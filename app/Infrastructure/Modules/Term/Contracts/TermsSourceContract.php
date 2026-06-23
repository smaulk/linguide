<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Term\Contracts;

use App\Core\Modules\Term\Dto\StoreTermDto;
use App\Infrastructure\Common\Exceptions\MissingResourceException;

interface TermsSourceContract
{
    /**
     * @param string $name имя ресурса
     * @return iterable<StoreTermDto>
     * @throws MissingResourceException
     */
    public function get(string $name): iterable;
}