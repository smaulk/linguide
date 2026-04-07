<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Mappers;

use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\Dictionary\Dto\WordImportDto;
use App\Core\Modules\Dictionary\Enums\PartOfSpeech;

final class WordImportMapper
{
    /**
     * @param array<string, string> $raw
     */
    public function mapRawToDto(array $raw): WordImportDto
    {
        return new WordImportDto(
            text: strtolower(trim($raw['text'])),
            pos: PartOfSpeech::from(strtolower(trim($raw['pos']))),
            level: LanguageLevel::fromName(strtoupper(trim($raw['level']))),
        );
    }
}