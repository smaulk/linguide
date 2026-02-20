<?php
declare(strict_types=1);

namespace App\Infrastructure\Ai\Sources\Contracts;

use App\Infrastructure\Support\Exceptions\MissingResourceException;

interface InstructionSourceContract
{
    /**
     * @param string $name имя ресурса
     * @return string содержимое ресурса
     * @throws MissingResourceException
     */
    public function get(string $name): string;
}