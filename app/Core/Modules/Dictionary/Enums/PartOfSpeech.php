<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Enums;

use App\Core\Common\Concerns\BaseEnum;

enum PartOfSpeech: string
{
    use BaseEnum;

    case NOUN         = 'noun';
    case VERB         = 'verb';
    case ADJECTIVE    = 'adjective';
    case ADVERB       = 'adverb';
    case PRONOUN      = 'pronoun';
    case PREPOSITION  = 'preposition';
    case CONJUNCTION  = 'conjunction';
    case DETERMINER   = 'determiner';
    case INTERJECTION = 'interjection';
    case NUMBER       = 'number';

    public function ru(): string
    {
        return match ($this) {
            self::NOUN         => 'существительное',
            self::VERB         => 'глагол',
            self::ADJECTIVE    => 'прилагательное',
            self::ADVERB       => 'наречие',
            self::PRONOUN      => 'местоимение',
            self::PREPOSITION  => 'предлог',
            self::CONJUNCTION  => 'союз',
            self::DETERMINER   => 'определитель',
            self::INTERJECTION => 'междометие',
            self::NUMBER       => 'число',
        };
    }

    /**
     * @return self[]
     */
    public static function trainable(): array
    {
        return [
            PartOfSpeech::NOUN,
            PartOfSpeech::VERB,
            PartOfSpeech::ADJECTIVE,
            PartOfSpeech::ADVERB,
        ];
    }
}