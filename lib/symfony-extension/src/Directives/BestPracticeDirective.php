<?php

/*
 * This file is part of the Docs Builder package.
 *
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyTools\GuidesExtension\Directives;

use phpDocumentor\Guides\RestructuredText\Directives\AbstractAdmonitionDirective;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\Rule;

class BestPracticeDirective extends AbstractAdmonitionDirective
{
    public function __construct(Rule $startingRule)
    {
        parent::__construct($startingRule, 'best-practice', 'Best Practice');
    }
}