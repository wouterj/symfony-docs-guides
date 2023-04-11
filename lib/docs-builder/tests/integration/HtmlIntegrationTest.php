<?php

namespace SymfonyDocsBuilder\Tests;

use PHPUnit\Framework\TestCase;
use SymfonyDocsBuilder\DocBuilder;
use SymfonyDocsBuilder\DocsKernel;
use SymfonyDocsBuilder\Test\HtmlAsserter;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Finder\Finder;

class HtmlIntegrationTest extends TestCase
{
    /** @dataProvider provideBlocks */
    public function testBlocks(string $sourceFile, string $expectedFile)
    {
        $generatedContents = DocsKernel::create()->get(DocBuilder::class)->buildString(file_get_contents($sourceFile));
        $generated = new \DOMDocument();
        $generated->loadHTML($generatedContents, \LIBXML_NOERROR);
        $generated->preserveWhiteSpace = false;
        $generatedHtml = $this->sanitizeHTML($generated->saveHTML());

        $expected = new \DOMDocument();
        $expectedContents = "<!DOCTYPE html>\n<html>\n<body>\n".file_get_contents($expectedFile)."\n</body>\n</html>";
        $expected->loadHTML($expectedContents, \LIBXML_NOERROR);
        $expected->preserveWhiteSpace = false;
        $expectedHtml = $this->sanitizeHTML($expected->saveHTML());

        $this->assertEquals($expectedHtml, $generatedHtml);
    }

    public static function provideBlocks(): iterable
    {
        foreach ((new Finder())->files()->in(__DIR__.'/fixtures/source/blocks') as $file) {
            yield [$file->getRealPath(), __DIR__.'/fixtures/expected/blocks/'.str_replace('.rst', '.html', $file->getRelativePathname())];
        }
    }

    private function sanitizeHTML(string $html): string
    {
        $html = implode("\n", array_map('trim', explode("\n", $html)));
        $html = preg_replace('# +#', ' ', $html);
        $html = preg_replace('# <#', '<', $html);
        $html = preg_replace('#> #', '>', $html);

        $html = substr($html, strpos($html, '<body>') + 6);
        $html = substr($html, 0, strpos($html, '</body>'));

        return trim($html);
    }
}
