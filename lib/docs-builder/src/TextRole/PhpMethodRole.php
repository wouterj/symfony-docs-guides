<?php

namespace SymfonyDocsBuilder\TextRole;

use SymfonyDocsBuilder\Build\BuildConfig;
use SymfonyDocsBuilder\Node\ExternalLinkToken;
use phpDocumentor\Guides\Nodes\InlineToken\InlineMarkupToken;
use phpDocumentor\Guides\ParserContext;
use phpDocumentor\Guides\RestructuredText\TextRoles\TextRole;

class PhpMethodRole implements TextRole
{
    public function __construct(
        private BuildConfig $buildConfig
    ) {
    }

    public function processNode(ParserContext $parserContext, string $id, string $role, string $content): InlineMarkupToken
    {
        [$fqcn, $method] = explode('::', $content, 2);

        $url = 'https://php.net/'.strtolower($fqcn).'.'.strtolower($method);
        $content .= '()';

        return new ExternalLinkToken($id, $url, $content, $content);
    }

    public function getName(): string
    {
        return 'phpmethod';
    }

    public function getAliases(): array
    {
        return [];
    }
}
