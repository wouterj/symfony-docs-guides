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

use SymfonyDocsBuilder\BuildConfig;
use SymfonyDocsBuilder\DependencyInjection\LazyNodeRendererFactory;
use SymfonyDocsBuilder\TwigEnvironmentFactory;
use SymfonyDocsBuilder\Twig\HighlighterExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use phpDocumentor\Guides\NodeRenderers\DefaultNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\DelegatingNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\SpanNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\TableNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\InMemoryNodeRendererFactory;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\NodeRenderers\TemplateNodeRenderer;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderer\OutputFormatRenderer;
use phpDocumentor\Guides\RestructuredText\NodeRenderers\Html\AdmonitionNodeRenderer;
use phpDocumentor\Guides\TemplateRenderer;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Guides\Twig\EnvironmentBuilder;
use phpDocumentor\Guides\Twig\TwigRenderer;
use phpDocumentor\Guides\Twig\TwigTemplateRenderer;

return static function (ContainerConfigurator $container) {
    $container ->services()
        ->defaults()->autowire()->autoconfigure()

        ->set(FilesystemLoader::class)
            ->args([
                [dirname(__DIR__, 3).'/vendor/phpdocumentor/guides/resources/template/html/guides']
            ])

        ->set(AssetsExtension::class)->tag('twig.extension')
        ->set(HighlighterExtension::class)->tag('twig.extension')

        ->set(EnvironmentBuilder::class)
            ->call('setEnvironmentFactory', [
                inline_service(TwigEnvironmentFactory::class)->args([
                    service(BuildConfig::class),
                    service(FilesystemLoader::class),
                    tagged_iterator('twig.extension'),
                ])
            ])

        ->set(SpanNodeRenderer::class)->tag('guides.node_renderer')

        ->set(TableNodeRenderer::class)->tag('guides.node_renderer')

        ->set(AdmonitionNodeRenderer::class)->tag('guides.node_renderer')

        ->set(InMemoryNodeRendererFactory::class)
            ->args([
                tagged_iterator('guides.node_renderer'),
                inline_service(DefaultNodeRenderer::class),
            ])

        ->alias(NodeRendererFactory::class, InMemoryNodeRendererFactory::class)

        ->set(DelegatingNodeRenderer::class)

        ->alias(NodeRenderer::class, DelegatingNodeRenderer::class)

        ->set(TwigTemplateRenderer::class)

        ->alias(TemplateRenderer::class, TwigTemplateRenderer::class)
    ;

    foreach ((new \phpDocumentor\Guides\Configuration())->htmlNodeTemplates() as $node => $template) {
        $container->services()
            ->set('guides.node_renderer.'.strtolower(substr($node, strrpos($node, '\\') + 1, -4)), TemplateNodeRenderer::class)
                ->args([
                    service(TemplateRenderer::class),
                    $template,
                    $node,
                ])
                ->tag('guides.node_renderer')
        ;
    }
};
