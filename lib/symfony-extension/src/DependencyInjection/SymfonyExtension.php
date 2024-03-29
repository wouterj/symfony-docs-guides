<?php

/*
 * This file is part of the Guides SymfonyExtension package.
 *
 * (c) Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyTools\GuidesExtension\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class SymfonyExtension extends Extension implements PrependExtensionInterface
{
    public function getAlias(): string
    {
        return 'symfony';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__, 2).'/config'));
        $loader->load('services.php');
        $loader->load('parser.php');
        $loader->load('renderer.php');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $templatesDir = \dirname(__DIR__, 2).'/resources/templates';

        $container->prependExtensionConfig('guides', [
            'default_code_language' => 'php',
            'themes' => [
                'symfonycom' => $templatesDir.'/symfonycom/html',
            ],
        ]);

        $container->prependExtensionConfig('code', [
            'languages' => [
                'php' => \dirname(__DIR__, 2).'/resources/highlight.php/php.json',
                'twig' => \dirname(__DIR__, 2).'/resources/highlight.php/twig.json',
            ],
            'aliases' => [
                'caddy' => 'plaintext',
                'env' => 'bash',
                'html+jinja' => 'twig',
                'html+twig' => 'twig',
                'jinja' => 'twig',
                'html+php' => 'html',
                'xml+php' => 'xml',
                'php-annotations' => 'php',
                'php-attributes' => 'php',
                'terminal' => 'bash',
                'rst' => 'markdown',
                'php-standalone' => 'php',
                'php-symfony' => 'php',
                'varnish4' => 'c',
                'varnish3' => 'c',
                'vcl' => 'c',
            ],
        ]);
    }
}
