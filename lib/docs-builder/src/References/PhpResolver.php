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
use phpDocumentor\Guides\Nodes\InlineToken\CrossReferenceNode;

final class PhpResolver implements Resolver
{
    public function supports(CrossReferenceNode $node, RenderContext $context): bool
    {
        return \in_array($node->getRole(), ['phpclass', 'phpmethod', 'phpfunction'], true);
    }

    public function resolve(CrossReferenceNode $node, RenderContext $context): ?ResolvedReference
    {
        [$fqcn, $method] = explode('::', $node->getUrl(), 2) + ['', ''];

        $label = $node->getText();
        if ($node->getUrl() === $label) {
            // no explicit label is set
            $label = $fqcn;
            if ($method) {
                $label .= '::'.$method;
            }
            if ('phpclass' !== $node->getRole()) {
                $label .= '()';
            }
        }

        $path = match ($node->getRole()) {
            'phpclass' => 'class.%s',
            'phpmethod' => '%s.%s',
            'phpfunction' => 'function.%s',
        };

        return new ResolvedReference(
            null,
            $label,
            sprintf('https://php.net/'.$path, str_replace('_', '-', strtolower($fqcn)), strtolower($method)),
            [
                'title' => $label,
            ]
        );
    }
}
