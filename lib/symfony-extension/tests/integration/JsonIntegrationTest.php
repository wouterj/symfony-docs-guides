<?php

namespace SymfonyTools\GuidesExtension\Tests;

use League\Flysystem\Adapter\Local;
use PHPUnit\Framework\TestCase;
use SymfonyTools\GuidesExtension\Build\BuildConfig;
use SymfonyTools\GuidesExtension\Build\DynamicBuildEnvironment;
use SymfonyTools\GuidesExtension\DocBuilder;
use SymfonyTools\GuidesExtension\DocsKernel;
use phpDocumentor\Guides\DependencyInjection\TestExtension;

class JsonIntegrationTest extends TestCase
{
    /**
     * @dataProvider getJsonTests
     */
    public function testJsonGeneration(string $filename, array $expectedData)
    {
        $kernel = DocsKernel::create([new TestExtension()]);

        $kernel->get(BuildConfig::class)->setOutputFormat('json');

        $buildEnvironment = new DynamicBuildEnvironment(new Local(__DIR__.'/fixtures/source/json'));
        $kernel->get(DocBuilder::class)->build($buildEnvironment);

        $actualFileData = json_decode($buildEnvironment->getOutputFilesystem()->read($filename.'.fjson'), true);
        $this->assertSame($expectedData, array_intersect_key($actualFileData, $expectedData), sprintf('Invalid data in file "%s"', $filename));
        foreach ($expectedData as $key => $expectedKeyData) {
            $this->assertArrayHasKey($key, $actualFileData, sprintf('Missing key "%s" in file "%s"', $key, $filename));
        }
    }

    public function getJsonTests()
    {
        yield 'index' => [
            'file' => 'index',
            'data' => [
                'parents' => [],
                'prev' => null,
                'next' => [
                    'title' => 'Dashboards',
                    'link' => 'dashboards.html',
                ],
                'title' => 'JSON Generation Test',
            ]
        ];

        yield 'dashboards' => [
            'file' => 'dashboards',
            'data' => [
                'parents' => [],
                'prev' => [
                    'title' => 'JSON Generation Test',
                    'link' => 'index.html',
                ],
                'next' => [
                    'title' => 'CRUD',
                    'link' => 'crud.html',
                ],
                'title' => 'Dashboards',
            ]
        ];

        yield 'design' => [
            'file' => 'design',
            'data' => [
                'parents' => [],
                'prev' => [
                    'title' => 'CRUD',
                    'link' => 'crud.html',
                ],
                'next' => [
                    'title' => 'Design Sub-Page',
                    'link' => 'design/sub-page.html',
                ],
                'title' => 'Design',
                'toc_options' => [
                    'maxDepth' => 2,
                    'numVisibleItems' => 5,
                    'size' => 'md'
                ],
                'toc' => [
                    [
                        'level' => 1,
                        'url' => 'design.html#section-1',
                        'page' => 'design',
                        'fragment' => 'section-1',
                        'title' => 'Section 1',
                        'children' => [
                            [
                                'level' => 2,
                                'url' => 'design.html#some-subsection',
                                'page' => 'design',
                                'fragment' => 'some-subsection',
                                'title' => 'Some subsection',
                                'children' => [],
                            ],
                            [
                                'level' => 2,
                                'url' => 'design.html#some-subsection-1',
                                'page' => 'design',
                                'fragment' => 'some-subsection-1',
                                'title' => 'Some subsection',
                                'children' => [],
                            ],
                        ],
                    ],
                    [
                        'level' => 1,
                        'url' => 'design.html#section-2',
                        'page' => 'design',
                        'fragment' => 'section-2',
                        'title' => 'Section 2',
                        'children' => [
                            [
                                'level' => 2,
                                'url' => 'design.html#some-subsection-2',
                                'page' => 'design',
                                'fragment' => 'some-subsection-2',
                                'title' => 'Some subsection',
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield 'crud' => [
           'file' => 'crud',
           'data' => [
               'parents' => [],
               'prev' => [
                   'title' => 'Dashboards',
                   'link' => 'dashboard.html',
               ],
               'next' => [
                   'title' => 'Design',
                   'link' => 'design.html',
               ],
               'title' => 'CRUD',
           ]
       ];

        yield 'design/sub-page' => [
            'file' => 'design/sub-page',
            'data' => [
                'parents' => [
                    [
                        'title' => 'Design',
                        'link' => '../design.html',
                    ],
                ],
                'prev' => [
                    'title' => 'Design',
                    'link' => '../design.html',
                ],
                'next' => [
                    'title' => 'Fields',
                    'link' => '../fields.html',
                ],
                'title' => 'Design Sub-Page',
            ]
        ];

        yield 'fields' => [
           'file' => 'fields',
           'data' => [
               'parents' => [],
               'prev' => [
                   'title' => 'Design Sub-Page',
                   'link' => 'design/sub-page.html',
               ],
               'next' => null,
               'title' => 'Fields',
           ]
       ];

        yield 'orphan' => [
          'file' => 'orphan',
          'data' => [
              'parents' => [],
              'prev' => null,
              'next' => null,
              'title' => 'Orphan',
          ]
      ];
    }
}
