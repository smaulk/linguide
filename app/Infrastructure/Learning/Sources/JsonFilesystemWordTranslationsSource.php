<?php
declare(strict_types=1);

namespace App\Infrastructure\Learning\Sources;

use App\Core\Modules\Words\Mappers\WordTranslationsMapper;
use App\Infrastructure\Learning\Concerns\ReadsFilesystemStream;
use App\Infrastructure\Learning\Sources\Contracts\WordTranslationsSourceContract;
use Illuminate\Contracts\Filesystem\Filesystem;

final readonly class JsonFilesystemWordTranslationsSource implements WordTranslationsSourceContract
{
    use ReadsFilesystemStream;

    public function __construct(
        private Filesystem $fs,
        private WordTranslationsMapper $mapper,
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

                $raw = json_decode($line, true, flags: JSON_THROW_ON_ERROR);

                yield $this->mapper->mapRawToDto($raw);
            }
        } finally {
            fclose($stream);
        }
    }
}