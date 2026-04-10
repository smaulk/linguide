<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Term\Sources;

use App\Core\Modules\Term\Mappers\TranslationsDatasetMapper;
use App\Infrastructure\Common\Concerns\ReadsFilesystemStream;
use App\Infrastructure\Modules\Term\Contracts\TranslationsSourceContract;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class JsonFilesystemTranslationsSource implements TranslationsSourceContract
{
    use ReadsFilesystemStream;

    public function __construct(
        private Filesystem $fs,
        private TranslationsDatasetMapper $mapper,
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
                    Log::warning('JsonFilesystemTranslationsSource failed row mapping.',
                        ['exception' => $e->getMessage()]
                    );
                    continue;
                }
            }
        } finally {
            fclose($stream);
        }
    }
}