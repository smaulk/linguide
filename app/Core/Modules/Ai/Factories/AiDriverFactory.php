<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Factories;

use App\Core\Modules\Ai\Dto\GeminiConfigDto;
use App\Core\Modules\Ai\Enums\AiDriverType;
use App\Core\Modules\Ai\Drivers\AiDriver;
use App\Core\Modules\Ai\Drivers\GeminiDriver;

class AiDriverFactory
{
    public static function make(AiDriverType $providerType, array $config = []): AiDriver
    {
        // Объединяем дефолтный конфиг с переданным, перезаписывая значения
        $config = array_merge($providerType->defaultConfig(), $config);

        return match ($providerType) {
            AiDriverType::GEMINI => new GeminiDriver(
                new GeminiConfigDto(
                    apiKey: $config['apiKey'],
                    apiVersion: $config['apiVersion'],
                    model: $config['model'],
                ),
            ),
        };
    }
}