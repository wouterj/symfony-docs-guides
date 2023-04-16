<?php

namespace SymfonyDocsBuilder\Twig;

use SymfonyDocsBuilder\Build\BuildConfig;
use SymfonyDocsBuilder\Highlighter\Highlighter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CodeExtension extends AbstractExtension
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
            new TwigFilter('fqcn', $this->fqcn(...), ['is_safe' => ['html']]),
        ];
    }

    public function highlight(string $code, ?string $language): string
    {
        return $this->highlighter->highlight($language ?? $this->buildConfig->getDefaultHighlightLanguage(), $code);
    }

    public function fqcn(string $fqcn): string
    {
        // some browsers can't break long <code> properly, so we inject a
        // `<wbr>` (word-break HTML tag) after some characters to help break those
        // We only do this for very long <code> (4 or more \\) to not break short
        // and common `<code>` such as App\Entity\Something
        if (substr_count($fqcn, '\\') >= 4) {
            // breaking before the backslask is what Firefox browser does
            $fqcn = str_replace('\\', '<wbr>\\', $fqcn);
        }

        return $fqcn;
    }
}
