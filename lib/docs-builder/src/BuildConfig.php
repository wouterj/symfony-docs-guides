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

use Flyfinder\Finder;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

final class BuildConfig
{
    private const SYMFONY_REPOSITORY_URL = 'https://github.com/symfony/symfony/blob/{symfonyVersion}/src/%s.php';

    private string $sourceDir;
    private ?Filesystem $sourceFilesystem = null;
    private string $outputDir;
    private ?Filesystem $outputFilesystem = null;
    private ?string $theme = 'blank';
    private string $format = 'html';

    public function __construct(
        private string $symfonyVersion = '6.1',
    ) {
        $this->sourceDir = getcwd();
        $this->outputDir = $this->sourceDir.'/_output';
    }

    public function setSourceDir(string $sourceDir): void
    {
        if ($sourceDir !== $this->sourceDir) {
            $this->sourceFilesystem = null;
        }
        $this->sourceDir = $sourceDir;
    }

    public function setOutputDir(string $outputDir): void
    {
        if ($outputDir !== $this->outputDir) {
            $this->outputFilesystem = null;
        }
        $this->outputDir = $outputDir;
    }

    public function setSymfonyVersion(string $symfonyVersion): void
    {
        $this->symfonyVersion = $symfonyVersion;
    }

    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }

    public function getSourceFilesystem(): Filesystem
    {
        return $this->sourceFilesystem ??= (new Filesystem(new Local($this->sourceDir)))->addPlugin(new Finder());
    }

    public function getOutputFilesystem(): Filesystem
    {
        return $this->outputFilesystem ??= new Filesystem(new Local($this->outputDir));
    }

    public function getSymfonyVersion(): string
    {
        return $this->symfonyVersion;
    }

    public function getSymfonyRepositoryUrl(): string
    {
        return str_replace('{symfonyVersion}', $this->getSymfonyVersion(), self::SYMFONY_REPOSITORY_URL);
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
