<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Dictionary\Sources;

use App\Core\Modules\Dictionary\Mappers\WordTranslationsDatasetMapper;
use App\Infrastructure\Common\Concerns\ReadsFilesystemStream;
use App\Infrastructure\Modules\Dictionary\Contracts\WordTranslationsSourceContract;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class JsonFilesystemWordTranslationsSource implements WordTranslationsSourceContract
{
    use ReadsFilesystemStream;

    public function __construct(
        private Filesystem $fs,
        private WordTranslationsDatasetMapper $mapper,
    ){}

    public function get(string $name): iterable
    {
        $fileName = $name . '.jsonl';
        $stream = $this->getFileStream($fileName);

        try {
            while (($line = fgets($stream)) !== false) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                try {
                    $raw = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
                    yield $this->mapper->mapRawToDto($raw);
                } catch (Throwable $e) {
                    Log::warning('JsonFilesystemWordTranslations failed row mapping: ' . $e->getMessage());
                    continue;
                }
            }
        } finally {
            fclose($stream);
        }
    }
}