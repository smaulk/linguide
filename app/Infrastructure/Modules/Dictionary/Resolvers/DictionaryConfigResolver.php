<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Dictionary\Resolvers;

use App\Core\Modules\Dictionary\Dto\DictionaryImportConfigDto;
use App\Infrastructure\Common\Classes\ArrayReader;
use App\Infrastructure\Common\Parents\Resolver;
use App\Infrastructure\Modules\Config\Contracts\ConfigSourceContract;
use App\Infrastructure\Modules\Config\Exceptions\InvalidConfigurationException;
use Throwable;

final class DictionaryConfigResolver extends Resolver
{
    public function __construct(private readonly ConfigSourceContract $configSource){}

    public function resolve(): ?DictionaryImportConfigDto
    {
        $config = $this->getConfig();
        if($config === null){
            return null;
        }

        $termsResource = $config->nullableString('terms');
        if ($termsResource === null) {
            return null;
        }

        try {
            return new DictionaryImportConfigDto(
                termsResource: $termsResource,
                translationsResource: $config->nullableString('translations'),
            );
        } catch (Throwable $e) {
            throw new InvalidConfigurationException(
                message: "Invalid configuration for dictionary: {$e->getMessage()}",
                previous: $e
            );
        }
    }

    private function getConfig(): ?ArrayReader
    {
        try{
            return new ArrayReader($this->configSource->get('dictionary.resources'));
        } catch (InvalidConfigurationException $e) {
            return null;
        }
    }
}