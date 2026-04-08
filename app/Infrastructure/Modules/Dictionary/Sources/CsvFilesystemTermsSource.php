<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Dictionary\Sources;

use App\Core\Modules\Dictionary\Mappers\TermDatasetMapper;
use App\Infrastructure\Common\Concerns\ReadsFilesystemStream;
use App\Infrastructure\Modules\Dictionary\Contracts\TermsSourceContract;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Throwable;
use ValueError;

final readonly class CsvFilesystemTermsSource implements TermsSourceContract
{
    use ReadsFilesystemStream;

    private const string DELIMITER = ',';

    public function __construct(private Filesystem $fs, private TermDatasetMapper $mapper){}

    public function get(string $name): iterable
    {
        $fileName = $name . '.csv';
        $stream = $this->getFileStream($fileName);

        try {
            fgetcsv($stream, 0, self::DELIMITER);

            while (($row = fgetcsv($stream, 0, self::DELIMITER)) !== false) {
                $raw = [
                    'type'  => $row[0] ?? null,
                    'text'  => $row[1] ?? null,
                    'level' => $row[2] ?? null,
                    'pos'   => $row[3] ?? null,
                ];

                if ($raw['type'] === null || $raw['text'] === null || $raw['level'] === null) {
                    continue;
                }

                try {
                    yield $this->mapper->mapRawToDto($raw);
                } catch (ValueError $e) {
                    continue;
                } catch (Throwable $e) {
                    Log::warning('CsvFilesystemTermSource failed row mapping.',
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