<?php

/*
 * This file is part of the Docs Builder package.
 *
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyDocsBuilder\Command;

use Flyfinder\Finder;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use phpDocumentor\Guides\Handlers\ParseDirectoryCommand;
use phpDocumentor\Guides\Handlers\RenderDocumentCommand;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\UrlGenerator;

class BuildDocsCommand extends Command
{
    public function __construct(
        private CommandBus $commandBus,
        private Metas $metas,
        private UrlGenerator $urlGenerator,
        private LoggerInterface $logger,
    ) {
        parent::__construct('build:docs');
    }

    protected function configure(): void
    {
        $this
            ->addArgument('source-dir', InputArgument::OPTIONAL, 'RST files Source directory', getcwd())
            ->addArgument('output-dir', InputArgument::OPTIONAL, 'HTML files output directory')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceFilesystem = new Filesystem(new Local($input->getArgument('source-dir')));
        $sourceFilesystem->addPlugin(new Finder());

        $targetFilesystem = new Filesystem(new Local($input->getArgument('output-dir')));

        $documents = $this->commandBus->handle(new ParseDirectoryCommand($sourceFilesystem, '/', 'rst'));
        foreach ($documents as $document) {
            try {
                $this->commandBus->handle(new RenderDocumentCommand(
                    $document,
                    RenderContext::forDocument($document, $sourceFilesystem, $targetFilesystem, '/', $this->metas, $this->urlGenerator, 'html')
                ));
            } catch (\Throwable $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
