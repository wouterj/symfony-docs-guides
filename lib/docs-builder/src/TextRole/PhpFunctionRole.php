<?php

namespace SymfonyDocsBuilder\TextRole;

use SymfonyDocsBuilder\Build\BuildConfig;
use SymfonyDocsBuilder\Node\ExternalLinkToken;
use phpDocumentor\Guides\Nodes\InlineToken\InlineMarkupToken;
use phpDocumentor\Guides\ParserContext;
use phpDocumentor\Guides\RestructuredText\TextRoles\TextRole;

class PhpFunctionRole implements TextRole
{
    public function __construct(
        private BuildConfig $buildConfig
    ) {
    }

    public function processNode(ParserContext $parserContext, string $id, string $role, string $content): InlineMarkupToken
    {
        $url = 'https://php.net/function.'.strtolower(str_replace('_', '-', $content));
        $content .= '()';

        return new ExternalLinkToken($id, $url, $content, $content);
    }

    public function getName(): string
    {
        return 'phpfunction';
    }

    public function getAliases(): array
    {
        return [];
    }
}
