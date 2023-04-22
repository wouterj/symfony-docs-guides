<?php

/*
 * This file is part of the Docs Builder package.
 *
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use phpDocumentor\Guides\RestructuredText\Parser\Productions\DocumentRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\EnumeratedListRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\DirectiveRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\DefinitionListRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\CommentRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\BlockQuoteRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\InlineMarkupRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\LiteralBlockRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\ListRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\LinkRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\GridTableRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\FieldListRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\SectionRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\ParagraphRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\SimpleTableRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\TransitionRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\TitleRule;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\RuleContainer;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()->autowire()

        ->bind(RuleContainer::class.' $bodyElements', service('phpdoc.guides.parser.rst.body_elements'))
        ->bind(RuleContainer::class.' $structuralElements', service('phpdoc.guides.parser.rst.structural_elements'))

        ->set('phpdoc.guides.parser.rst.body_elements', RuleContainer::class)
        ->set('phpdoc.guides.parser.rst.structural_elements', RuleContainer::class)

        ->set(DocumentRule::class)

        ->set(BlockQuoteRule::class)->tag('phpdoc.guides.parser.rst.body_element', ['priority' => BlockQuoteRule::PRIORITY])
        ->set(CommentRule::class)->tag('phpdoc.guides.parser.rst.body_element', ['priority' => CommentRule::PRIORITY])
        ->set(DefinitionListRule::class)->tag('phpdoc.guides.parser.rst.body_element', ['priority' => DefinitionListRule::PRIORITY])
        ->set(DirectiveRule::class)
            ->arg('$directives', tagged_iterator('guides.directive'))
            ->tag('phpdoc.guides.parser.rst.body_element', ['priority' => DirectiveRule::PRIORITY])
        ->set(EnumeratedListRule::class)
            ->arg('$productions', service('phpdoc.guides.parser.rst.body_elements'))
            ->tag('phpdoc.guides.parser.rst.body_element', ['priority' => EnumeratedListRule::PRIORITY])
        ->set(FieldListRule::class)
            ->arg('$productions', service('phpdoc.guides.parser.rst.body_elements'))
            ->arg('$fieldListItemRules', tagged_iterator('phpdoc.guides.parser.rst.fieldlist'))
            ->tag('phpdoc.guides.parser.rst.body_element', ['priority' => FieldListRule::PRIORITY])
        ->set(GridTableRule::class)
            ->arg('$productions', service('phpdoc.guides.parser.rst.body_elements'))
            ->tag('phpdoc.guides.parser.rst.body_element', ['priority' => GridTableRule::PRIORITY])
        ->set(LinkRule::class)->tag('phpdoc.guides.parser.rst.body_element', ['priority' => LinkRule::PRIORITY])
        ->set(ListRule::class)
            ->arg('$productions', service('phpdoc.guides.parser.rst.body_elements'))
            ->tag('phpdoc.guides.parser.rst.body_element', ['priority' => ListRule::PRIORITY])
        ->set(LiteralBlockRule::class)->tag('phpdoc.guides.parser.rst.body_element', ['priority' => LiteralBlockRule::PRIORITY])
        ->set(ParagraphRule::class)->tag('phpdoc.guides.parser.rst.body_element', ['priority' => ParagraphRule::PRIORITY])
        ->set(SimpleTableRule::class)
            ->arg('$productions', service('phpdoc.guides.parser.rst.body_elements'))
            ->tag('phpdoc.guides.parser.rst.body_element', ['priority' => SimpleTableRule::PRIORITY])

        ->set(InlineMarkupRule::class)
        ->set(TitleRule::class)

        ->set(TransitionRule::class)->tag('phpdoc.guides.parser.rst.structural_element', ['priority' => TransitionRule::PRIORITY])
        ->set(SectionRule::class)->tag('phpdoc.guides.parser.rst.structural_element', ['priority' => SectionRule::PRIORITY])
    ;
};
