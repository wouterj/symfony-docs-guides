<?php

namespace SymfonyDocsBuilder\Node;

use phpDocumentor\Guides\Nodes\InlineToken\AbstractLinkToken;

class ExternalLinkToken extends AbstractLinkToken
{
    public function __construct(
        string $id,
        private string $url,
        private string $text,
        private string $title,
    ) {
        parent::__construct('external-link', $id, []);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
