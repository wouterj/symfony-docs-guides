<?php

namespace SymfonyDocsBuilder\Build;

use League\Flysystem\Filesystem;

interface BuildEnvironment
{
    public function getSourceFilesystem(): Filesystem;
    public function getOutputFilesystem(): Filesystem;
}
