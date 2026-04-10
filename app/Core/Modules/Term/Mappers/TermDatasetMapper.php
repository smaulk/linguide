<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Mappers;

use App\Core\Modules\Term\Enums\TermType;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\Term\Dto\TermDatasetDto;
use App\Core\Modules\Term\Enums\PartOfSpeech;
use UnexpectedValueException;

final class TermDatasetMapper
{
    /**
     * @param array<string, string|null> $raw
     */
    public function mapRawToDto(array $raw): TermDatasetDto
    {
        $prepared = $this->prepareRaw($raw);

        $pos = $prepared['pos'] !== ''
            ? PartOfSpeech::from(strtolower($prepared['pos']))
            : PartOfSpeech::UNKNOWN;

        return new TermDatasetDto(
            text: strtolower($prepared['text']),
            type: TermType::from(strtolower($prepared['type'])),
            level: LanguageLevel::fromName(strtoupper($prepared['level'])),
            pos: $pos,
        );
    }

    /**
     * @param array<string, string|null> $raw
     * @return array<string, string>
     */
    private function prepareRaw(array $raw): array
    {
        $prepared = [
            'type'  => trim($raw['type'] ?? ''),
            'text'  => trim($raw['text'] ?? ''),
            'level' => trim($raw['level'] ?? ''),
            'pos'   => trim($raw['pos'] ?? ''),
        ];

        if ($prepared['type'] === '' || $prepared['text'] === '' || $prepared['level'] === '') {
            throw new UnexpectedValueException(
                'Term data is incomplete or invalid: ' . json_encode($prepared)
            );
        }

        return $prepared;
    }
}