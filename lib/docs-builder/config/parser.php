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

use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\DocumentRule;
use phpDocumentor\Guides\RestructuredText\Span\SpanParser;
use phpDocumentor\Guides\Parser;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()->autowire()

        ->set(SpanParser::class)

        ->set(MarkupLanguageParser::class)
            ->args([
                service(DocumentRule::class),
                tagged_iterator('guides.directive')
            ])

        ->set(Parser::class)
            ->args([
                '$parserStrategies' => [service(MarkupLanguageParser::class)],
            ])
            ->public()
    ;
};
