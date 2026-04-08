<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Dictionary\Writers;

use App\Core\Modules\Dictionary\Dto\TermTranslationDatasetDto;
use App\Core\Modules\Dictionary\Mappers\TranslationsDatasetMapper;
use App\Infrastructure\Modules\Dictionary\Contracts\TranslationsWriterContract;
use Illuminate\Contracts\Filesystem\Filesystem;

final readonly class JsonFilesystemTranslationsWriter implements TranslationsWriterContract
{
    public function __construct(
        private Filesystem $fs,
        private TranslationsDatasetMapper $mapper,
    ){}

    public function write(string $resourceName, array $terms): void
    {
        $fileName = $resourceName . '.jsonl';

        $lines = [];
        foreach ($terms as $term) {
            $termJson = $this->getJson($term);
            if ($termJson !== false) {
                $lines[] = $termJson;
            }
        }

        $json = implode(PHP_EOL, $lines);
        $this->fs->append($fileName, $json);
    }

    private function getJson(TermTranslationDatasetDto $term): false|string
    {
        return json_encode(
            $this->mapper->mapDtoToRaw($term),
            JSON_UNESCAPED_UNICODE
        );
    }
}