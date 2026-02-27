<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Response\Markdown\Render;

use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableRow;
use League\CommonMark\Extension\Table\TableSection;

final class MarkdownTableRenderer
{
    private const string SEPARATOR   = "*──────────────────────*";
    private const string EMPTY_VALUE = '─';

    public static function render(Table $node, MarkdownAstRenderer $astRenderer): string
    {
        [$headers, $rows] = self::extractTable($node);
        if ($rows === []) {
            return '';
        }

        return self::buildText($headers, $rows, $astRenderer);
    }

    /**
     * @return array{0: TableCell[], 1: array<int, TableCell[]>}
     */
    private static function extractTable(Table $node): array
    {
        $headers = [];
        $rows = [];

        foreach ($node->children() as $section) {
            if (!$section instanceof TableSection) {
                continue;
            }

            foreach (self::rows($section) as $cells) {
                if ($section->isHead()) {
                    $headers = $cells;
                } else {
                    $rows[] = $cells;
                }
            }
        }

        return [$headers, $rows];
    }

    /**
     * @return iterable<TableCell[]>
     */
    private static function rows(TableSection $section): iterable
    {
        foreach ($section->children() as $row) {
            if (!$row instanceof TableRow) {
                continue;
            }

            $cells = [];
            foreach ($row->children() as $cell) {
                if ($cell instanceof TableCell) {
                    $cells[] = $cell;
                }
            }

            yield $cells;
        }
    }

    /**
     * @param TableCell[] $headers
     * @param array<int, TableCell[]> $rows
     * @param MarkdownAstRenderer $astRenderer
     * @return string
     */
    private static function buildText(array $headers, array $rows, MarkdownAstRenderer $astRenderer): string
    {
        $blocks = [];
        foreach ($rows as $row) {
            $blocks[] = self::renderRowBlock($headers, $row, $astRenderer);
        }

        if ($blocks === []) {
            return '';
        }

        return implode("\n", $blocks) . "\n" . self::SEPARATOR . "\n\n";
    }

    /**
     * @param TableCell[] $headers
     * @param TableCell[] $row
     * @param MarkdownAstRenderer $astRenderer
     * @return string
     */
    private static function renderRowBlock(array $headers, array $row, MarkdownAstRenderer $astRenderer): string
    {
        $lines = [self::SEPARATOR, ''];
        foreach ($headers as $colIndex => $headerCell) {
            $header = '*' . $astRenderer->render($headerCell) . '*';

            $valueCell = $row[$colIndex] ?? null;
            $value = $valueCell instanceof TableCell
                ? $astRenderer->render($valueCell)
                : self::EMPTY_VALUE;

            $lines[] = $header . ': ' . $value;
        }
        $lines[] = '';

        return implode("\n", $lines);
    }
}