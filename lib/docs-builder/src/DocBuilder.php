<?php

namespace SymfonyDocsBuilder;

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use League\Tactician\CommandBus;
use SymfonyDocsBuilder\Build\BuildConfig;
use SymfonyDocsBuilder\Build\MemoryBuildEnvironment;
use phpDocumentor\Guides\Handlers\CompileDocumentsCommand;
use phpDocumentor\Guides\Handlers\ParseDirectoryCommand;
use phpDocumentor\Guides\Handlers\RenderDocumentCommand;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\UrlGenerator;

final class DocBuilder
{
    public function __construct(
        private CommandBus $commandBus,
        private BuildConfig $buildConfig,
        private Metas $metas,
        private UrlGenerator $urlGenerator,
    ) {
    }

    public function buildString(string $contents): string
    {
        $this->buildConfig->setTheme('symfonycom');

        $buildEnvironment = new MemoryBuildEnvironment();
        $buildEnvironment->getSourceFilesystem()->write('/index.rst', $contents);

        /** @var list<DocumentNode> $documents */
        $documents = $this->commandBus->handle(new ParseDirectoryCommand($buildEnvironment->getSourceFilesystem(), '/', 'rst'));

        $this->commandBus->handle(new CompileDocumentsCommand($documents));

        foreach ($documents as $document) {
            $this->commandBus->handle(new RenderDocumentCommand(
                $document,
                RenderContext::forDocument(
                    $document,
                    $buildEnvironment->getSourceFilesystem(),
                    $buildEnvironment->getOutputFilesystem(),
                    '/',
                    $this->metas,
                    $this->urlGenerator,
                    'html'
                )
            ));
        }

        return $buildEnvironment->getOutputFilesystem()->read('/index.html');
    }
}
