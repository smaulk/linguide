<?php
declare(strict_types=1);

namespace App\Infrastructure\Learning\Sources;

use App\Core\Modules\Words\Mappers\WordImportMapper;
use App\Infrastructure\Learning\Concerns\ReadsFilesystemStream;
use App\Infrastructure\Learning\Sources\Contracts\WordSourceContract;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class CsvFilesystemWordSource implements WordSourceContract
{
    use ReadsFilesystemStream;

    private const string DELIMITER = ',';

    public function __construct(
        private Filesystem $fs,
        private WordImportMapper $mapper,
    ){}

    public function get(string $name): iterable
    {
        $fileName = $name . '.csv';
        $stream = $this->getFileStream($fileName);

        try {
            fgetcsv($stream, 0, self::DELIMITER);

            while (($row = fgetcsv($stream, 0, self::DELIMITER)) !== false) {
                $raw = [
                    'text'  => $row[0] ?? null,
                    'level' => $row[1] ?? null,
                    'pos'   => $row[2] ?? null,
                ];

                if ($raw['text'] === null || $raw['level'] === null || $raw['pos'] === null) {
                    continue;
                }

                try {
                    yield $this->mapper->mapRawToDto($raw);
                } catch (Throwable $e) {
                    Log::warning('CsvFilesystemWordSource failed row mapping: ' . $e->getMessage());
                    continue;
                }
            }
        } finally {
            fclose($stream);
        }
    }
}