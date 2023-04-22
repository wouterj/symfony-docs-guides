<?php

namespace SymfonyDocsBuilder;

use League\Tactician\CommandBus;
use SymfonyDocsBuilder\Build\BuildConfig;
use SymfonyDocsBuilder\Build\BuildEnvironment;
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

    public function build(BuildEnvironment $buildEnvironment): void
    {
        $this->buildConfig->setTheme('symfonycom');

        /** @var list<DocumentNode> $documents */
        $documents = $this->commandBus->handle(new ParseDirectoryCommand($buildEnvironment->getSourceFilesystem(), '/', 'rst'));

        $documents = $this->commandBus->handle(new CompileDocumentsCommand($documents));

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
    }

    public function buildString(string $contents): string
    {
        $buildEnvironment = new MemoryBuildEnvironment();
        $buildEnvironment->getSourceFilesystem()->write('/index.rst', $contents);

        $this->build($buildEnvironment);

        return $buildEnvironment->getOutputFilesystem()->read('/index.html');
    }
}
