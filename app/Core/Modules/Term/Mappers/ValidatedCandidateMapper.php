<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Mappers;

use App\Core\Modules\Term\Dto\ValidatedCandidateDto;
use App\Core\Modules\Term\Enums\PartOfSpeech;
use App\Core\Modules\Term\Enums\TermType;

final class ValidatedCandidateMapper
{
    /**
     * @param array<array<string, mixed>> $rawTerms
     * @return ValidatedCandidateDto[]
     */
    public function mapRawArrayToDtoArray(array $rawTerms): array
    {
        $data = [];
        foreach ($rawTerms as $rawTerm) {
            $data[] = $this->mapRawToDto($rawTerm);
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $rawTerm
     * @return ValidatedCandidateDto
     */
    public function mapRawToDto(array $rawTerm): ValidatedCandidateDto
    {
        $posRaw = $rawTerm['pos'] ?? null;

        $posArr = is_array($posRaw)
            ? array_map(fn(string $item) => PartOfSpeech::from($item), $posRaw)
            : null;

        $isValid = (bool)($rawTerm['is_valid'] ?? false);

        return new ValidatedCandidateDto(
            term: strtolower(trim($rawTerm['term'])),
            isValid: $isValid,
            type: $isValid ? TermType::from(strtolower(trim($rawTerm['type']))) : null,
            baseForm: $isValid ? strtolower(trim($rawTerm['base_form'])): null,
            pos: $posArr,
        );
    }
}