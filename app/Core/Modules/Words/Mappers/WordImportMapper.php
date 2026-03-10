<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Mappers;

use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\Words\Dto\WordImportDto;
use App\Core\Modules\Words\Enums\PartOfSpeechType;

final class WordImportMapper
{
    public function mapRawToDto(array $raw): WordImportDto
    {
        return new WordImportDto(
            text: strtolower(trim($raw['text'])),
            pos: PartOfSpeechType::from(strtolower(trim($raw['pos']))),
            level: LanguageLevel::from(strtoupper(trim($raw['level']))),
        );
    }
}