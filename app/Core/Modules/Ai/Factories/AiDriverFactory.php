<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Factories;

use App\Core\Modules\Ai\Contracts\AiDriverContract;
use App\Core\Modules\Ai\Drivers\GeminiDriver;
use App\Core\Modules\Ai\Enums\AiDriverType;
use App\Infrastructure\Ai\Resolvers\AiDriverConfigResolver;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;

final readonly class AiDriverFactory
{
    public function __construct(
        private Container $container,
        private AiDriverConfigResolver $configResolver,
    ){}

    /**
     * @param AiDriverType $driverType
     * @return AiDriverContract
     * @throws BindingResolutionException
     */
    public function make(AiDriverType $driverType): AiDriverContract
    {
        $config = $this->configResolver->resolve($driverType);

        return match ($driverType) {
            AiDriverType::GEMINI => $this->container->make(
                GeminiDriver::class,
                ['config' => $config]
            ),
        };
    }
}