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

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Plugins\LockingMiddleware;
use SymfonyDocsBuilder\DependencyInjection\CommandLocator;
use phpDocumentor\Guides\FileCollector;
use phpDocumentor\Guides\Handlers\CompileDocumentsCommand;
use phpDocumentor\Guides\Handlers\CompileDocumentsHandler;
use phpDocumentor\Guides\Handlers\ParseDirectoryCommand;
use phpDocumentor\Guides\Handlers\ParseDirectoryHandler;
use phpDocumentor\Guides\Handlers\ParseFileCommand;
use phpDocumentor\Guides\Handlers\ParseFileHandler;
use phpDocumentor\Guides\Handlers\RenderDocumentCommand;
use phpDocumentor\Guides\Handlers\RenderDocumentHandler;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()->autowire()

        ->set(ParseFileHandler::class)
            ->tag('guides.command', ['command' => ParseFileCommand::class])

        ->set(ParseDirectoryHandler::class)
            ->args([inline_service(FileCollector::class)->autowire()])
            ->tag('guides.command', ['command' => ParseDirectoryCommand::class])

        ->set(CompileDocumentsHandler::class)
            ->tag('guides.command', ['command' => CompileDocumentsCommand::class])

        ->set(RenderDocumentHandler::class)
            ->tag('guides.command', ['command' => RenderDocumentCommand::class])

        ->set(CommandBus::class)
            ->args([[
                inline_service(CommandHandlerMiddleware::class)
                    ->args([
                        inline_service(ClassNameExtractor::class),
                        inline_service(CommandLocator::class)->args([tagged_locator('guides.command', 'command')]),
                        inline_service(HandleInflector::class),
                    ]),
                inline_service(LockingMiddleware::class),
            ]])
    ;
};
