<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Response\Markdown\Render;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Parser\MarkdownParser;

final readonly class MarkdownConverter
{
    private MarkdownParser $parser;
    private MarkdownAstRenderer $renderer;

    public function __construct()
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $this->parser = new MarkdownParser($environment);
        $this->renderer = new MarkdownAstRenderer();
    }

    /**
     * Преобразует Markdown текст в Telegram MarkdownV2 формат
     *
     * @throws CommonMarkException
     */
    public function convert(string $markdown): string
    {
        $document = $this->parser->parse($markdown);

        return $this->renderer->render($document);
    }
}