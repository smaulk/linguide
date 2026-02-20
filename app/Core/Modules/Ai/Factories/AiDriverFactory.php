<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Factories;

use App\Core\Modules\Ai\Contracts\AiDriverContract;
use App\Core\Modules\Ai\Drivers\GeminiDriver;
use App\Core\Modules\Ai\Enums\AiDriverType;
use App\Infrastructure\Ai\Resolvers\AiDriverConfigResolver;

final readonly class AiDriverFactory
{
    public function __construct(
        private AiDriverConfigResolver $configResolver,
    ){}

    /**
     * @param AiDriverType $driverType
     * @return AiDriverContract
     */
    public function make(AiDriverType $driverType): AiDriverContract
    {
        $config = $this->configResolver->resolve($driverType);

        return match ($driverType) {
            AiDriverType::GEMINI => new GeminiDriver($config),
        };
    }
}