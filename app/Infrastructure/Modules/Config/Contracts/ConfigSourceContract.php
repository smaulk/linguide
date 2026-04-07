<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Config\Contracts;

use App\Infrastructure\Modules\Config\Exceptions\InvalidConfigurationException;

interface ConfigSourceContract
{
    /**
     * @param string $key ключ конфига
     * @return array<string, mixed> тело конфига
     * @throws InvalidConfigurationException
     */
    public function get(string $key): array;
}