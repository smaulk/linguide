<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Mappers;

use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\Words\Dto\WordImportDto;
use App\Core\Modules\Words\Enums\PartOfSpeech;

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
            level: LanguageLevel::from(strtoupper(trim($raw['level']))),
        );
    }
}