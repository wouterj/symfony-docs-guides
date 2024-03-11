<?php

/*
 * This file is part of the Guides SymfonyExtension package.
 *
 * (c) Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyTools\GuidesExtension\Renderer;

use phpDocumentor\Guides\Handlers\RenderCommand;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Renderer\TypeRenderer;
use phpDocumentor\Guides\Renderer\UrlGenerator\UrlGeneratorInterface;

class JsonRenderer implements TypeRenderer
{
    public function __construct(
        private NodeRendererFactory $nodeRendererFactory,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function render(RenderCommand $renderCommand): void
    {
        $projectRenderContext = RenderContext::forProject(
            $renderCommand->getProjectNode(),
            $renderCommand->getDocumentArray(),
            $renderCommand->getOrigin(),
            $renderCommand->getDestination(),
            $renderCommand->getDestinationPath(),
            $renderCommand->getOutputFormat(),
        )->withIterator($renderCommand->getDocumentIterator());

        foreach ($projectRenderContext->getIterator() as $documentNode) {
            $context = $projectRenderContext->withDocument($documentNode);
            $html = implode(
                "\n",
                array_map(fn (Node $node): string => $this->nodeRendererFactory->get($node)->render($node, $context), $documentNode->getChildren())
            );

            $prevDocument = $context->getIterator()->previousNode();
            $nextDocument = $context->getIterator()->nextNode();
            $context->getDestination()->put(
                $context->getDestinationPath().'/'.$context->getCurrentFileName().'.fjson',
                json_encode([
                    'parents' => [],
                    'prev' => $prevDocument ? [
                        'title' => $prevDocument->getTitle()?->toString() ?? '',
                        'link' => substr($this->urlGenerator->createFileUrl($context, $prevDocument->getFilePath()), 0, -4).'html',
                    ] : null,
                    'next' => $nextDocument ? [
                        'title' => $nextDocument->getTitle()?->toString() ?? '',
                        'link' => substr($this->urlGenerator->createFileUrl($context, $nextDocument->getFilePath()), 0, -4).'html',
                    ] : null,
                    'title' => $documentNode->getTitle()?->toString() ?? '',
                    'body' => $html,
                ], \JSON_PRETTY_PRINT)
            );
        }
    }
}
