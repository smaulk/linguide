<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Response\Markdown;

final class MarkdownSplitter
{
    /** Лимит на длину сообщения в Телеграм */
    private const int TELEGRAM_LIMIT = 4096;
    /** Безопасный лимит с запасом */
    private const int LIMIT = 3500;

    /**
     * Разбивает Markdown-текст на чанки для Telegram с учётом:
     *  - красивого разбиения по строкам
     *  - word-wrap для длинных строк
     *  - fenced code blocks
     *  - inline code
     *
     * @return string[]
     */
    public function split(string $text): array
    {
        // Если длина текста не превышает лимит, возвращаем его
        if (mb_strlen($text) <= self::TELEGRAM_LIMIT) {
            return [trim($text)];
        }

        $lines = preg_split('/\R/u', $text) ?: [];

        $chunks = [];
        $current = '';
        $length = 0;

        $inCodeBlock = false;
        $codeLang = '';
        $inInlineCode = false;

        foreach ($lines as $i => $line) {
            $isLast = $i === count($lines) - 1;
            $toAdd = $line . ($isLast ? '' : "\n");
            $addLen = mb_strlen($toAdd);

            // Определяем fenced code block
            if (preg_match('/^\s*```(\S*)/', $line, $m)) {
                $inCodeBlock = !$inCodeBlock;
                /** @phpstan-ignore-next-line */
                $codeLang = $inCodeBlock ? ($m[1] ?? '') : '';
                $inInlineCode = false;
            }

            // Трекинг inline code (чётность количества ` в строке — учитывает пары и незавершённые)
            if (!$inCodeBlock) {
                $backticks = substr_count($line, '`');
                if ($backticks % 2 === 1) {
                    $inInlineCode = !$inInlineCode;
                }
            }

            // Одна строка длиннее лимита
            if ($addLen > self::LIMIT) {
                if ($current !== '') {
                    $current = $this->closeOpenMarkup($current, $inCodeBlock, $inInlineCode);
                    $chunks[] = rtrim($current);

                    $current = $this->reopenMarkup($inCodeBlock, $codeLang, $inInlineCode);
                    $length = mb_strlen($current);
                }

                $chunks = array_merge($chunks, $this->splitLongLine($line, $inCodeBlock, $codeLang, $inInlineCode));
                continue;
            }

            // Обычная строка — проверяем лимит
            if ($length + $addLen > self::LIMIT) {
                $current = $this->closeOpenMarkup($current, $inCodeBlock, $inInlineCode);
                $chunks[] = rtrim($current);

                $current = $this->reopenMarkup($inCodeBlock, $codeLang, $inInlineCode);
                $length = mb_strlen($current);
            }

            $current .= $toAdd;
            $length += $addLen;
        }

        if ($current !== '') {
            $chunks[] = rtrim($current);
        }

        return $chunks;
    }

    private function closeOpenMarkup(string $text, bool $inCodeBlock, bool $inInlineCode): string
    {
        if ($inCodeBlock) {
            $text .= "\n```";
        }
        if ($inInlineCode) {
            $text .= '`';
        }
        return $text;
    }

    private function reopenMarkup(bool $inCodeBlock, string $codeLang, bool $inInlineCode): string
    {
        $s = '';
        if ($inCodeBlock) {
            $s .= "```{$codeLang}\n";
        }
        if ($inInlineCode) {
            $s .= '`';
        }
        return $s;
    }

    /**
     * Разбиение одной сверхдлинной строки
     * @return string[]
     */
    private function splitLongLine(string $line, bool $inCodeBlock, string $codeLang, bool $inInline): array
    {
        $chunks = [];
        $remaining = $line;

        while (mb_strlen($remaining) > 0) {
            $max = self::LIMIT - ($inCodeBlock ? 8 : 4); // запас под закрывающие теги
            $part = mb_substr($remaining, 0, $max);
            $remaining = mb_substr($remaining, $max);
            $chunk = $part;

            if ($remaining === '') {
                $chunks[] = $chunk;
                break;
            }

            // Закрываем и переоткрываем при необходимости
            if ($inCodeBlock) {
                $chunk .= "\n```";
                $remaining = "```{$codeLang}\n" . $remaining;
            } elseif ($inInline) {
                $chunk .= '`';
                $remaining = '`' . $remaining;
            } else {
                // Красивое разделение строки по пробелу
                $lastSpace = mb_strrpos($part, ' ');
                // Если расположение последнего пробела дальше 70% строки, делим строку по пробелу
                if ($lastSpace !== false && $lastSpace > $max * 0.7) {
                    $chunk = mb_substr($part, 0, $lastSpace);
                    $remaining = mb_substr($part, $lastSpace + 1) . $remaining;
                }
            }

            $chunks[] = $chunk;
        }

        return $chunks;
    }
}