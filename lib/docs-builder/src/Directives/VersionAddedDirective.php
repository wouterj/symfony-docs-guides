<?php

/*
 * This file is part of the Docs Builder package.
 *
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyDocsBuilder\Directives;

use phpDocumentor\Guides\RestructuredText\Directives\AbstractAdmonitionDirective;
use phpDocumentor\Guides\RestructuredText\Span\SpanParser;

class VersionAddedDirective extends AbstractAdmonitionDirective
{
    public function __construct(SpanParser $spanParser)
    {
        parent::__construct('versionadded', 'Version Added', $spanParser);
    }
}
