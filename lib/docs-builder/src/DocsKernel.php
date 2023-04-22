<?php

/*
 * This file is part of the Docs Builder package.
 *
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyDocsBuilder;

use SymfonyDocsBuilder\DependencyInjection\GuidesExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use phpDocumentor\Guides\DependencyInjection\Compiler\NodeRendererPass;
use phpDocumentor\Guides\DependencyInjection\Compiler\ParserRulesPass;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactoryAware;

final class DocsKernel
{
    public function __construct(
        private Container $container
    ) {}

    public static function create(array $extensions = []): self
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new ParserRulesPass());
        $container->addCompilerPass(new NodeRendererPass());

        foreach (array_merge($extensions, [self::createDefaultExtension()]) as $extension) {
            $container->registerExtension($extension);
            $container->loadFromExtension($extension->getAlias());
        }

        $container->compile();

        return new self($container);
    }

    /**
     * @template T
     * @param class-string<T> $fqcn
     * @return T
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function get(string $fqcn): object
    {
        return $this->container->get($fqcn);
    }

    private static function createDefaultExtension(): ExtensionInterface
    {
        return new class extends Extension {
            public function getAlias(): string
            {
                return 'default';
            }

            public function load(array $configs, ContainerBuilder $container): void
            {
                $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/config'));
                $loader->load('services.php');
                $loader->load('parser.php');
                $loader->load('parser_rules.php');
                $loader->load('parser_directives.php');
                $loader->load('compiler.php');
                $loader->load('renderer.php');
                $loader->load('command_bus.php');

                $container->registerForAutoconfiguration(NodeRendererFactoryAware::class)
                    ->addMethodCall('setNodeRendererFactory', [new Reference(NodeRendererFactory::class)]);
            }
        };
    }
}
