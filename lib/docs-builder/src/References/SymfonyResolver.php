<?php

/*
 * This file is part of the Docs Builder package.
 *
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyDocsBuilder\References;

use phpDocumentor\Guides\References\ResolvedReference;
use phpDocumentor\Guides\References\Resolver\Resolver;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Span\CrossReferenceNode;

class SymfonyResolver implements Resolver
{
    public function supports(CrossReferenceNode $node, RenderContext $context): bool
    {
        return \in_array($node->getRole(), ['class', 'method', 'namespace'], true);
    }

    public function resolve(CrossReferenceNode $node, RenderContext $context): ?ResolvedReference
    {
        [$fqcn, $method] = explode('::', $node->getUrl(), 2) + ['', ''];
        $fqcn = str_replace('\\\\', '\\', $fqcn);

        $label = substr($fqcn, strrpos($fqcn, '\\') + 1);
        if ($method) {
            $label .= '::'.$method.'()';
        }

        return new ResolvedReference(
            null,
            $label,
            sprintf('https://github.com/symfony/symfony/tree/6.1/src/%s.php', str_replace('\\', '/', $fqcn))
        );
    }
}
