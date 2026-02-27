<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Response\Markdown\Render;

final class MarkdownStyleCleaner
{
    private const array STYLE_PATTERNS = [
        '/\|\|(.*?)\|\|/us', // ||spoiler||
        '/~~(.*?)~~/us', // ~~strikethrough~~
        '/\*\*(.*?)\*\*/us', // **bold**
        '/__(.*?)__/us', // __underline__
        '/(?<![\p{L}\p{N}])\*(.*?)\*(?![\p{L}\p{N}])/us', // *bold* / *italic*
        '/(?<![\p{L}\p{N}_])_(.*?)_(?![\p{L}\p{N}_])/us', // _italic_
    ];

    /**
     * Очищает текст от стилизации (bold, italic, underline, spoiler, strike)
     */
    public static function clean(string $text): string
    {
        return trim(
            preg_replace(self::STYLE_PATTERNS, '$1', $text) ?? ''
        );
    }
}