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

use SymfonyDocsBuilder\Build\BuildConfig;
use phpDocumentor\Guides\References\ResolvedReference;
use phpDocumentor\Guides\References\Resolver\Resolver;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Nodes\InlineToken\CrossReferenceNode;

final class SymfonyResolver implements Resolver
{
    public function __construct(
        private BuildConfig $buildConfig,
    ) {}

    public function supports(CrossReferenceNode $node, RenderContext $context): bool
    {
        return \in_array($node->getRole(), ['class', 'method', 'namespace'], true);
    }

    public function resolve(CrossReferenceNode $node, RenderContext $context): ?ResolvedReference
    {
        [$fqcn, $method] = explode('::', $node->getUrl(), 2) + ['', ''];
        $fqcn = str_replace('\\\\', '\\', $fqcn);

        $label = $node->getText();
        if ($node->getUrl() === $label) {
            // no explicit label is set, create one based on the URL
            if ($method) {
                $label = $method.'()';
            } else {
                $label = substr($fqcn, (strrpos($fqcn, '\\') ?: -1) + 1);
            }
        }

        $filename = str_replace('\\', '/', $fqcn);
        if ('namespace' !== $node->getRole()) {
            $filename .= '.php';
        }

        return new ResolvedReference(
            null,
            $label,
            sprintf($this->buildConfig->getSymfonyRepositoryUrl(), $filename),
            [
                'title' => $fqcn.($method ? '::'.$method.'()' : '')
            ]
        );
    }
}
