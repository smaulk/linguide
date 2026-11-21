<?php

declare(strict_types=1);

namespace App\Interfaces\Telegram\Response\Markdown\Render;

final class MarkdownEscaper
{
    private const array ESCAPES = [
        '\\' => '\\\\',
        '_' => '\\_',
        '*' => '\\*',
        '[' => '\\[',
        ']' => '\\]',
        '(' => '\\(',
        ')' => '\\)',
        '~' => '\\~',
        '`' => '\\`',
        '>' => '\\>',
        '#' => '\\#',
        '+' => '\\+',
        '-' => '\\-',
        '=' => '\\=',
        '|' => '\\|',
        '{' => '\\{',
        '}' => '\\}',
        '.' => '\\.',
        '!' => '\\!',
    ];

    public static function escape(string $text): string
    {
        return strtr($text, self::ESCAPES);
    }

    public static function escapeCode(string $text): string
    {
        return strtr($text, [
            '\\' => '\\\\',
            '`' => '\\`',
        ]);
    }
}
