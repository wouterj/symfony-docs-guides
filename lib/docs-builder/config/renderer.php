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
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use phpDocumentor\Guides\NodeRenderers\DefaultNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\DocumentNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\SpanNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\TableNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\InMemoryNodeRendererFactory;
use phpDocumentor\Guides\NodeRenderers\TemplateNodeRenderer;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderer\OutputFormatRenderer;
use phpDocumentor\Guides\Renderer\TemplateRenderer;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Guides\Twig\EnvironmentBuilder;

return static function (ContainerConfigurator $container) {
    $container ->services()
        ->defaults()->autowire()

        ->set(FilesystemLoader::class)
            ->args([
                [dirname(__DIR__, 3).'/vendor/phpdocumentor/guides/resources/template']
            ])

        ->set(AssetsExtension::class)
            ->tag('twig.extension')

        ->set(EnvironmentBuilder::class)
            ->call('setEnvironmentFactory', [
                inline_service(TwigEnvironmentFactory::class)->args([
                    service(BuildConfig::class),
                    service(FilesystemLoader::class),
                    tagged_iterator('twig.extension'),
                ])
            ])

        ->set(DocumentNodeRenderer::class)->tag('guides.node_renderer')

        ->set(SpanNodeRenderer::class)->tag('guides.node_renderer')

        ->set(TableNodeRenderer::class)->tag('guides.node_renderer')

        ->set(InMemoryNodeRendererFactory::class)
            ->args([
                tagged_iterator('guides.node_renderer'),
                inline_service(DefaultNodeRenderer::class),
            ])

        ->set(OutputFormatRenderer::class)
            ->args([
                'html',
                inline_service(LazyNodeRendererFactory::class)->tag('container.service_subscriber'),
                inline_service(TemplateRenderer::class)->autowire(),
            ])

        ->set(Renderer::class)
            ->args([[service(OutputFormatRenderer::class)]])
            ->public()
    ;

    foreach ((new \phpDocumentor\Guides\Configuration())->htmlNodeTemplates() as $node => $template) {
        $container->services()
            ->set('guides.node_renderer'.substr($node, strrpos($node, '\\') + 1), TemplateNodeRenderer::class)
                ->args([
                    service(Renderer::class),
                    $template,
                    $node,
                ])
                ->tag('guides.node_renderer')
        ;
    }
};
