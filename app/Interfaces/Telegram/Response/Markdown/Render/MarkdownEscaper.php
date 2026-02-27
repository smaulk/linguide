<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Response\Markdown\Render;

final class MarkdownEscaper
{
    private const array SPECIAL = [
        '_', '*', '[', ']', '(', ')', '~', '`', '>', '#',
        '+', '-', '=', '|', '{', '}', '.', '!'
    ];

    public static function escape(string $text): string
    {
        return str_replace(
            self::SPECIAL,
            array_map(fn($c) => '\\' . $c, self::SPECIAL),
            $text
        );
    }
}