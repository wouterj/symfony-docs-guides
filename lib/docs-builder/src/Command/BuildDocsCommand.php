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
use Flyfinder\Path;
use Flyfinder\Specification\InPath;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use SymfonyDocsBuilder\BuildConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use phpDocumentor\Guides\Handlers\CompileDocumentsCommand;
use phpDocumentor\Guides\Handlers\ParseDirectoryCommand;
use phpDocumentor\Guides\Handlers\ParseFileCommand;
use phpDocumentor\Guides\Handlers\RenderDocumentCommand;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\UrlGenerator;

class BuildDocsCommand extends Command
{
    public function __construct(
        private CommandBus $commandBus,
        private BuildConfig $buildConfig,
        private Metas $metas,
        private UrlGenerator $urlGenerator,
        private LoggerInterface $logger,
    ) {
        parent::__construct('build:docs');
    }

    protected function configure(): void
    {
        $this
            ->addOption('symfony-version', null, InputOption::VALUE_REQUIRED, 'The version of Symfony')
            ->addOption('no-theme', null, InputOption::VALUE_NONE, 'Use the default theme instead of the styled one')
            ->addOption('clear-cache', null, InputOption::VALUE_NONE)
            ->addArgument('source-dir', InputArgument::OPTIONAL, 'RST files Source directory', getcwd())
            ->addArgument('output-dir', InputArgument::OPTIONAL, 'HTML files output directory')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setupBuildConfig($input);

        if ($input->getOption('clear-cache') || !is_file(sys_get_temp_dir().'/guides.cache')) {
            $documents = $this->parse($this->buildConfig->getSourceFilesystem());
            file_put_contents(sys_get_temp_dir().'/guides.cache', serialize($documents));
        } else {
            $documents = unserialize(file_get_contents(sys_get_temp_dir().'/guides.cache'));
        }

        $documents = $this->compile($documents);
        $success = $this->render($documents);

        $this->renderThemeAssets();

        return $success ? self::SUCCESS : self::FAILURE;
    }

    private function parse(Filesystem $sourceFilesystem): array
    {
        return $this->commandBus->handle(new ParseDirectoryCommand($sourceFilesystem, '/', 'rst'));
    }

    private function compile(array $documents): array
    {
        return $this->commandBus->handle(new CompileDocumentsCommand($documents));
    }

    private function render(array $documents): bool
    {
        $success = true;
        foreach ($documents as $document) {
            //if (!str_starts_with($document->getFilePath(), 'security')) {
            //    continue;
            //}

            try {
                $this->commandBus->handle(new RenderDocumentCommand(
                    $document,
                    RenderContext::forDocument(
                        $document,
                        $this->buildConfig->getSourceFilesystem(),
                        $this->buildConfig->getOutputFilesystem(),
                        '/',
                        $this->metas,
                        $this->urlGenerator,
                        'html'
                    )
                ));
            } catch (\Throwable $e) {
                $success = false;
                $this->logger->error($e->getMessage());
            }
        }

        return $success;
    }

    private function renderThemeAssets(): void
    {
        $assetsFilesystem = new Filesystem(new Local(__DIR__.'/../../templates/'.$this->buildConfig->getTheme()));
        $assetsFilesystem->addPlugin(new Finder());

        $outputFilesystem = $this->buildConfig->getOutputFilesystem();

        if ($outputFilesystem->has('assets')) {
            $outputFilesystem->deleteDir('assets');
        }

        foreach ($assetsFilesystem->find(new InPath(new Path('assets'))) as $file) {
            $outputFilesystem->write(
                $file['path'],
                $assetsFilesystem->read($file['path'])
            );
        }
    }

    private function setupBuildConfig(InputInterface $input): void
    {
        $this->buildConfig->setSourceDir($input->getArgument('source-dir'));
        $this->buildConfig->setOutputDir($input->getArgument('output-dir'));
        if ($sfVersion = $input->getOption('symfony-version')) {
            $this->buildConfig->setSymfonyVersion($sfVersion);
        }

        if ($input->getOption('no-theme')) {
            $this->buildConfig->setTheme(null);
        } else {
            $this->buildConfig->setTheme('rtd');
        }
    }
}
