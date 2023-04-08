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

use phpDocumentor\Guides\Compiler\Compiler;
use phpDocumentor\Guides\Compiler\DocumentNodeTraverser;
use phpDocumentor\Guides\Compiler\NodeTransformers\CollectLinkTargetsTransformer;
use phpDocumentor\Guides\Compiler\NodeTransformers\CustomNodeTransformerFactory;
use phpDocumentor\Guides\Compiler\NodeTransformers\TocNodeTransformer;
use phpDocumentor\Guides\Compiler\Passes\MetasPass;
use phpDocumentor\Guides\Compiler\Passes\TransformerPass;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()->autowire()

        ->set(MetasPass::class)->tag('guides.compiler_pass')
        ->set(TransformerPass::class)->tag('guides.compiler_pass')

        ->set(TocNodeTransformer::class)->tag('guides.node_transformer')
        ->set(CollectLinkTargetsTransformer::class)->tag('guides.node_transformer')

        ->set(DocumentNodeTraverser::class)
            ->args([
                inline_service(CustomNodeTransformerFactory::class)
                    ->args([tagged_iterator('guides.node_transformer')])
            ])

        ->set(Compiler::class)
            ->args([
                tagged_iterator('guides.compiler_pass')
            ])
    ;
};
