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

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use SymfonyDocsBuilder\Application;
use SymfonyDocsBuilder\Command\BuildDocsCommand;
use SymfonyDocsBuilder\References\SymfonyResolver;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Parser;
use phpDocumentor\Guides\References\ReferenceResolver;
use phpDocumentor\Guides\References\Resolver\DocResolver;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\UrlGenerator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()->autowire()

        ->set(OutputInterface::class, ConsoleOutput::class)

        ->set(Application::class)->public()
            ->call('add', [inline_service(BuildDocsCommand::class)->autowire()])

        ->set(LoggerInterface::class, ConsoleLogger::class)

        ->set(EventDispatcherInterface::class, EventDispatcher::class)

        ->set(Metas::class)

        ->set(DocResolver::class)->tag('guides.reference_resolver')

        ->set(SymfonyResolver::class)->tag('guides.reference_resolver')

        ->set(ReferenceResolver::class)
            ->args([tagged_iterator('guides.reference_resolver')])

        ->set(UrlGenerator::class)

        ->set(Parser::class)->args([
            '$parserStrategies' => [inline_service(MarkupLanguageParser::class)->factory([MarkupLanguageParser::class, 'createInstance'])]
        ])
    ;
};
