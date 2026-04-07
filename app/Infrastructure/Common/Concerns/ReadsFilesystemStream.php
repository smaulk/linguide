<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Concerns;

use App\Infrastructure\Common\Exceptions\MissingResourceException;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * @property-read Filesystem $fs
 */
trait ReadsFilesystemStream
{
    /**
     * @param string $fileName
     * @return resource
     * @throws MissingResourceException
     */
    private function getFileStream(string $fileName)
    {
        if (!$this->fs->exists($fileName)) {
            throw new MissingResourceException("File not found: $fileName");
        }

        $stream = $this->fs->readStream($fileName);

        if ($stream === null) {
            throw new MissingResourceException("Unable to read file: $fileName");
        }

        return $stream;
    }
}