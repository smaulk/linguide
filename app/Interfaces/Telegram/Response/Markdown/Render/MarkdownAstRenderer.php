<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Response\Markdown\Render;

use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;

final readonly class MarkdownAstRenderer
{
    public function render(Node $node): string
    {
        $parts = [];
        foreach ($node->children() as $child) {
            $parts[] = $this->renderNode($child);
        }

        return implode('', $parts);
    }

    private function renderNode(Node $node): string
    {
        return match (true) {
            $node instanceof Heading    =>
                '*' . $this->render($node) . "*\n\n",

            $node instanceof Paragraph  =>
                $this->render($node) . "\n\n",

            $node instanceof Text       =>
                MarkdownEscaper::escape($node->getLiteral()),

            $node instanceof Strong     =>
                '*' . $this->render($node) . '*',

            $node instanceof Emphasis   =>
                '_' . $this->render($node) . '_',

            $node instanceof Code       =>
                '`' . MarkdownStyleCleaner::clean($node->getLiteral()) . '`',

            $node instanceof FencedCode =>
                "```\n" . $node->getLiteral() . "\n```\n",

            $node instanceof Link       =>
                '[' . $this->render($node) . '](' .
                MarkdownEscaper::escape($node->getUrl()) . ')',

            $node instanceof ListItem   =>
                '• ' . trim($this->render($node)) . "\n",

            $node instanceof ListBlock  =>
                $this->render($node) . "\n",

            $node instanceof BlockQuote =>
                $this->renderQuote($node),

            $node instanceof Table      =>
                MarkdownTableRenderer::render($node, $this),

            default                     =>
                $this->render($node),
        };
    }

    private function renderQuote(Node $node): string
    {
        $text = trim($this->render($node));
        $lines = explode("\n", $text);
        $lines = array_map(fn($l) => '> ' . $l, $lines);

        return implode("\n", $lines) . "\n\n";
    }
}