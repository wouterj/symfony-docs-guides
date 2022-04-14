#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use SymfonyDocsBuilder\BuildConfig;
use SymfonyDocsBuilder\DocsKernel;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Parser;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\UrlGenerator;

$kernel = DocsKernel::create();

$parser = $kernel->get(Parser::class);
$documentNode = $parser->parse(<<<EOT
This is *a test* of parsing a bit of `reStructured Text <https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html>`_.
EOT);

$renderer = $kernel->get(Renderer::class);
$out = $renderer->renderDocument($documentNode, RenderContext::forDocument(
    $documentNode,
    new Filesystem(new MemoryAdapter()),
    $outfs = new Filesystem(new MemoryAdapter()),
    '',
    $kernel->get(Metas::class),
    $kernel->get(UrlGenerator::class),
    'html'
));

echo trim($out).PHP_EOL;
