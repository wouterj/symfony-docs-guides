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

final class DocsKernel
{
    public function __construct(
        private Container $container
    ) {}

    public static function create(array $extensions = [])
    {
        $container = new ContainerBuilder();
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
                $loader->load('renderer.php');
                $loader->load('command_bus.php');
            }
        };
    }
}
