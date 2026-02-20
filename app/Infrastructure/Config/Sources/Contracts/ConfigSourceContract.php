<?php
declare(strict_types=1);

namespace App\Infrastructure\Config\Sources\Contracts;

use App\Infrastructure\Config\Exceptions\InvalidConfigurationException;

interface ConfigSourceContract
{
    /**
     * @param string $key ключ конфига
     * @return array<string, mixed> тело конфига
     * @throws InvalidConfigurationException
     */
    public function get(string $key): array;
}