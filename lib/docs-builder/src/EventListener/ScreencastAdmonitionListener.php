<?php

namespace SymfonyDocsBuilder\EventListener;

use phpDocumentor\Guides\Event\PreParseDocument;

/**
 * TODO: remove this temporary fix when Symfony Docs are updated to use the new '.. screencast::' directive
 */
class ScreencastAdmonitionListener
{
    public function onPreParseDocument(PreParseDocument $event): void
    {
        $event->setContents(str_replace('.. admonition:: Screencast', '.. screencast::', $event->getContents()));
    }
}
