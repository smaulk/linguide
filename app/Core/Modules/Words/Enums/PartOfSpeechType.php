<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Enums;

use App\Core\Common\Concerns\BaseEnum;

enum PartOfSpeechType: string
{
    use BaseEnum;

    case NOUN = 'noun';
    case VERB = 'verb';
    case ADJECTIVE = 'adjective';
    case ADVERB = 'adverb';
    case PREPOSITION = 'preposition';
}