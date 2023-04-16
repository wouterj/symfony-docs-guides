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
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use SymfonyDocsBuilder\Application;
use SymfonyDocsBuilder\Build\BuildConfig;
use SymfonyDocsBuilder\Command\BuildDocsCommand;
use SymfonyDocsBuilder\Directives\VersionAddedDirective;
use SymfonyDocsBuilder\DocBuilder;
use SymfonyDocsBuilder\EventListener\ScreencastAdmonitionListener;
use SymfonyDocsBuilder\Highlighter\Highlighter;
use SymfonyDocsBuilder\References\PhpResolver;
use SymfonyDocsBuilder\References\SymfonyResolver;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use phpDocumentor\Guides\Event\PreParseDocument;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\References\ReferenceResolver;
use phpDocumentor\Guides\References\Resolver\DocResolver;
use phpDocumentor\Guides\References\Resolver\RefResolver;
use phpDocumentor\Guides\UrlGenerator;
use phpDocumentor\Guides\UrlGeneratorInterface;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()->autowire()

        ->set(OutputInterface::class, ConsoleOutput::class)

        ->set(Application::class)->public()
            ->call('add', [inline_service(BuildDocsCommand::class)->autowire()])
            ->call('setDispatcher', [service(EventDispatcher::class)])

        ->set(LoggerInterface::class, ConsoleLogger::class)

        ->set(EventDispatcher::class)
            ->call('addListener', [PreParseDocument::class, [inline_service(ScreencastAdmonitionListener::class), 'onPreParseDocument']])
        ->alias(EventDispatcherInterface::class, EventDispatcher::class)

        ->set(Highlighter::class)
            ->args([
                inline_service(HighlightPHP::class)
                    ->call('registerLanguage', ['php', __DIR__.'/../templates/highlight.php/php.json', true])
                    ->call('registerLanguage', ['twig', __DIR__.'/../templates/highlight.php/twig.json', true])
            ])

        ->set(Metas::class)

        ->set(BuildConfig::class)

        ->set(DocResolver::class)->tag('guides.reference_resolver')

        ->set(RefResolver::class)->tag('guides.reference_resolver')

        ->set(SymfonyResolver::class)->tag('guides.reference_resolver')

        ->set(PhpResolver::class)->tag('guides.reference_resolver')

        ->set(ReferenceResolver::class)
            ->args([tagged_iterator('guides.reference_resolver')])

        ->set(UrlGenerator::class)
        ->alias(UrlGeneratorInterface::class, UrlGenerator::class)

        ->set(DocBuilder::class)->public()
    ;
};
