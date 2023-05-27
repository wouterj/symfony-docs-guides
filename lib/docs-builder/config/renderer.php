<?php

/*
 * This file is part of the Docs Builder package.
 *
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Highlight\Highlighter as HighlightPHP;
use SymfonyDocsBuilder\Highlighter\Highlighter;
use SymfonyDocsBuilder\NodeRenderer\CodeNodeRenderer;
use SymfonyDocsBuilder\References\PhpResolver;
use SymfonyDocsBuilder\References\SymfonyResolver;
use SymfonyDocsBuilder\Twig\CodeExtension;
use SymfonyDocsBuilder\Twig\UrlExtension;
use Twig\Extension\ExtensionInterface;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\References\Resolver\Resolver;

return static function (ContainerConfigurator $container) {
    $container ->services()
        ->defaults()->autowire()->autoconfigure()
        ->instanceof(ExtensionInterface::class)->tag('twig.extension')
        ->instanceof(Resolver::class)->tag('phpdoc.guides.reference.resolver')
        ->instanceof(NodeRenderer::class)->tag('phpdoc.guides.noderenderer.html')

        ->set(CodeExtension::class)
        ->set(UrlExtension::class)

        ->set(SymfonyResolver::class)
        ->set(PhpResolver::class)

        ->set(CodeNodeRenderer::class)

        ->set(Highlighter::class)
            ->args([
                inline_service(HighlightPHP::class)
                    ->call('registerLanguage', ['php', dirname(__DIR__, 1).'/templates/highlight.php/php.json', true])
                    ->call('registerLanguage', ['twig', dirname(__DIR__, 1).'/templates/highlight.php/twig.json', true])
            ])
    ;
};
