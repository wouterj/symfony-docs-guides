<?php

namespace SymfonyDocsBuilder\Twig;

use SymfonyDocsBuilder\Build\BuildConfig;
use SymfonyDocsBuilder\Highlighter\Highlighter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HighlighterExtension extends AbstractExtension
{
    public function __construct(
        private Highlighter $highlighter,
        private BuildConfig $buildConfig
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('highlight', $this->highlight(...), ['is_safe' => ['html']]),
        ];
    }

    public function highlight(string $code, ?string $language): string
    {
        return $this->highlighter->highlight($language ?? $this->buildConfig->getDefaultHighlightLanguage(), $code);
    }
}
