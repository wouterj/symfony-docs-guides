<?php

namespace SymfonyDocsBuilder\Twig;

use Highlight\Highlighter;
use Psr\Log\LoggerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HighlighterExtension extends AbstractExtension
{
    private const LANGUAGES_MAPPING = [
        'env' => 'bash',
        'html+jinja' => 'twig',
        'html+twig' => 'twig',
        'jinja' => 'twig',
        'html+php' => 'html',
        'xml+php' => 'xml',
        'php-annotations' => 'php',
        'php-attributes' => 'php',
        'terminal' => 'bash',
        'rst' => 'markdown',
        'php-standalone' => 'php',
        'php-symfony' => 'php',
        'varnish4' => 'c',
        'varnish3' => 'c',
        'vcl' => 'c',
    ];

    public function __construct(
        private Highlighter $highlighter,
        private LoggerInterface $logger
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('highlight', $this->highlight(...), ['is_safe' => ['html']]),
        ];
    }

    public function highlight(string $code, string $language): string
    {
        try {
            return $this->highlighter->highlight($language, self::LANGUAGES_MAPPING[$code] ?? $code)->value;
        } catch (\Throwable $e) {
            $this->logger->error('Error highlighting {language} code block', [
                'language' => $language,
                'code' => $code,
                'error' => $e,
            ]);

            return $code;
        }
    }
}
