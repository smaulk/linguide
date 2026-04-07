<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Dictionary\Writers;

use App\Core\Modules\Dictionary\Dto\WordDatasetDto;
use App\Core\Modules\Dictionary\Mappers\WordTranslationsDatasetMapper;
use App\Infrastructure\Modules\Dictionary\Contracts\WordTranslationsWriterContract;
use Illuminate\Contracts\Filesystem\Filesystem;

final readonly class JsonFilesystemWordTranslationsWriter implements WordTranslationsWriterContract
{
    public function __construct(
        private Filesystem $fs,
        private WordTranslationsDatasetMapper $mapper,
    ){}

    public function write(string $resourceName, array $words): void
    {
        $fileName = $resourceName . '.jsonl';

        $lines = [];
        foreach ($words as $word) {
            $wordJson = $this->getJson($word);
            if ($wordJson !== false) {
                $lines[] = $wordJson;
            }
        }

        $json = implode(PHP_EOL, $lines);
        $this->fs->append($fileName, $json);
    }

    private function getJson(WordDatasetDto $word): false|string
    {
        return json_encode(
            $this->mapper->mapDtoToRaw($word),
            JSON_UNESCAPED_UNICODE
        );
    }
}