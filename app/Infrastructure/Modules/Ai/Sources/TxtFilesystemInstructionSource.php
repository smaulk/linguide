<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Ai\Sources;

use App\Infrastructure\Common\Exceptions\MissingResourceException;
use App\Infrastructure\Modules\Ai\Contracts\InstructionSourceContract;
use Illuminate\Contracts\Filesystem\Filesystem;

final readonly class TxtFilesystemInstructionSource implements InstructionSourceContract
{
    public function __construct(private Filesystem $fs){}

    public function get(string $name): string
    {
        $fileName = $name . '.txt';
        $content = $this->fs->get($fileName);

        if ($content === null) {
            throw new MissingResourceException("Instruction file does not exist: {$fileName}");
        }

        $content = trim($content);
        if ($content === '') {
            throw new MissingResourceException("Instruction file '{$fileName}' is empty");
        }

        return $content;
    }
}