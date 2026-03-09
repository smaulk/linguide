<?php
declare(strict_types=1);

namespace App\Infrastructure\Learning\Sources;

use App\Core\Modules\Words\Dto\RawWordDto;
use App\Infrastructure\Learning\Concerns\ReadsFilesystemStream;
use App\Infrastructure\Learning\Sources\Contracts\WordSourceContract;
use Illuminate\Contracts\Filesystem\Filesystem;

final readonly class CsvFilesystemWordSource implements WordSourceContract
{
    use ReadsFilesystemStream;

    private const string DELIMITER = ',';

    public function __construct(private Filesystem $fs){}

    public function get(string $name): iterable
    {
        $fileName = $name . '.csv';
        $stream = $this->getFileStream($fileName);

        try {
            fgetcsv($stream, 0, self::DELIMITER);

            while (($row = fgetcsv($stream, 0, self::DELIMITER)) !== false) {
                $text = $row[0] ?? null;
                $level = $row[1] ?? null;
                $pos = $row[2] ?? null;

                if ($text === null || $level === null || $pos === null) {
                    continue;
                }

                yield new RawWordDto(
                    text: trim($text),
                    pos: trim($pos),
                    level: trim($level),
                );
            }
        } finally {
            fclose($stream);
        }
    }
}