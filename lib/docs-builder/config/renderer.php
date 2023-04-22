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

use SymfonyDocsBuilder\Build\BuildConfig;
use SymfonyDocsBuilder\DependencyInjection\LazyNodeRendererFactory;
use SymfonyDocsBuilder\NodeRenderer\CodeNodeRenderer;
use SymfonyDocsBuilder\Twig\EnvironmentFactory;
use SymfonyDocsBuilder\Twig\CodeExtension;
use SymfonyDocsBuilder\Twig\UrlExtension;
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
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderer\OutputFormatRenderer;
use phpDocumentor\Guides\RestructuredText\NodeRenderers\Html\AdmonitionNodeRenderer;
use phpDocumentor\Guides\RestructuredText\NodeRenderers\Html\SidebarNodeRenderer;
use phpDocumentor\Guides\RestructuredText\NodeRenderers\Html\TopicNodeRenderer;
use phpDocumentor\Guides\RestructuredText\Nodes\VersionChangeNode;
use phpDocumentor\Guides\TemplateRenderer;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Guides\Twig\EnvironmentBuilder;
use phpDocumentor\Guides\Twig\TwigRenderer;
use phpDocumentor\Guides\Twig\TwigTemplateRenderer;

foreach ([3, 1] as $ps) {
    if (is_dir($vendor = dirname(__DIR__, $ps).'/vendor')) {
        break;
    }
}

return static function (ContainerConfigurator $container) use ($vendor) {
    $container ->services()
        ->defaults()->autowire()->autoconfigure()

        ->set(FilesystemLoader::class)
            ->args([
                [$vendor.'/phpdocumentor/guides/resources/template/html/guides']
            ])

        ->set(AssetsExtension::class)->tag('twig.extension')
        ->set(CodeExtension::class)->tag('twig.extension')
        ->set(UrlExtension::class)->tag('twig.extension')

        ->set(EnvironmentBuilder::class)
            ->call('setEnvironmentFactory', [
                inline_service(EnvironmentFactory::class)->args([
                    service(BuildConfig::class),
                    service(FilesystemLoader::class),
                    tagged_iterator('twig.extension'),
                ])
            ])

        ->set(SpanNodeRenderer::class)->tag('guides.node_renderer')

        ->set(TableNodeRenderer::class)->tag('guides.node_renderer')

        ->set(TopicNodeRenderer::class)->tag('guides.node_renderer')

        ->set(AdmonitionNodeRenderer::class)->tag('guides.node_renderer')

        ->set(SidebarNodeRenderer::class)->tag('guides.node_renderer')

        ->set(CodeNodeRenderer::class)->tag('guides.node_renderer')

        ->set('guides.node_renderer.version_changes', TemplateNodeRenderer::class)
            ->args([
                service(TemplateRenderer::class),
                'body/version-change.html.twig',
                VersionChangeNode::class,
            ])
            ->tag('guides.node_renderer')

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
        if (CodeNode::class === $node) {
            continue;
        }

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
