<?php

declare(strict_types=1);

namespace App\Core\Modules\Term\Tests\Unit;

use App\Core\Common\Parents\UnitTest;
use App\Interfaces\Telegram\Response\Markdown\Render\MarkdownEscaper;

final class MarkdownEscaperTest extends UnitTest
{
    public function test_it_escapes_markdown_v2_reserved_characters_and_backslashes(): void
    {
        self::assertSame(
            'well\\-being \\\\path \\_word\\_',
            MarkdownEscaper::escape('well-being \\path _word_'),
        );
    }

    public function test_it_escapes_only_backticks_and_backslashes_in_code(): void
    {
        self::assertSame('a\\`b\\\\c', MarkdownEscaper::escapeCode('a`b\\c'));
    }
}
