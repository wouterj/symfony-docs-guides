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

use SymfonyDocsBuilder\Directives\BestPracticeDirective;
use SymfonyDocsBuilder\Directives\ScreencastDirective;
use phpDocumentor\Guides\RestructuredText\Directives\AdmonitionDirective;
use phpDocumentor\Guides\RestructuredText\Directives\CautionDirective;
use phpDocumentor\Guides\RestructuredText\Directives\ClassDirective;
use phpDocumentor\Guides\RestructuredText\Directives\CodeBlock;
use phpDocumentor\Guides\RestructuredText\Directives\ContainerDirective;
use phpDocumentor\Guides\RestructuredText\Directives\DeprecatedDirective;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Directives\Figure;
use phpDocumentor\Guides\RestructuredText\Directives\HintDirective;
use phpDocumentor\Guides\RestructuredText\Directives\Image;
use phpDocumentor\Guides\RestructuredText\Directives\ImportantDirective;
use phpDocumentor\Guides\RestructuredText\Directives\IncludeDirective;
use phpDocumentor\Guides\RestructuredText\Directives\IndexDirective;
use phpDocumentor\Guides\RestructuredText\Directives\Meta;
use phpDocumentor\Guides\RestructuredText\Directives\NoteDirective;
use phpDocumentor\Guides\RestructuredText\Directives\RawDirective;
use phpDocumentor\Guides\RestructuredText\Directives\Replace;
use phpDocumentor\Guides\RestructuredText\Directives\RoleDirective;
use phpDocumentor\Guides\RestructuredText\Directives\SeeAlsoDirective;
use phpDocumentor\Guides\RestructuredText\Directives\SidebarDirective;
use phpDocumentor\Guides\RestructuredText\Directives\TipDirective;
use phpDocumentor\Guides\RestructuredText\Directives\Title;
use phpDocumentor\Guides\RestructuredText\Directives\Toctree;
use phpDocumentor\Guides\RestructuredText\Directives\TopicDirective;
use phpDocumentor\Guides\RestructuredText\Directives\Uml;
use phpDocumentor\Guides\RestructuredText\Directives\VersionAddedDirective;
use phpDocumentor\Guides\RestructuredText\Directives\VersionChangedDirective;
use phpDocumentor\Guides\RestructuredText\Directives\WarningDirective;
use phpDocumentor\Guides\RestructuredText\Directives\Wrap;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Toc\GlobSearcher;
use phpDocumentor\Guides\RestructuredText\Toc\ToctreeBuilder;
use phpDocumentor\Guides\UrlGenerator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()->autowire()

        ->set(AdmonitionDirective::class)->tag('guides.directive')
        ->set(CautionDirective::class)->tag('guides.directive')
        ->set(ClassDirective::class)->tag('guides.directive')
        ->set(CodeBlock::class)->tag('guides.directive')
        ->set(ContainerDirective::class)->tag('guides.directive')
        ->set(Figure::class)->tag('guides.directive')
        ->set(HintDirective::class)->tag('guides.directive')
        ->set(Image::class)->tag('guides.directive')
        ->set(ImportantDirective::class)->tag('guides.directive')
        ->set(IncludeDirective::class)->tag('guides.directive')
        ->set(IndexDirective::class)->tag('guides.directive')
        ->set(Meta::class)->tag('guides.directive')
        ->set(NoteDirective::class)->tag('guides.directive')
        ->set(RawDirective::class)->tag('guides.directive')
        ->set(Replace::class)->tag('guides.directive')
        ->set(RoleDirective::class)->tag('guides.directive')
        ->set(SeeAlsoDirective::class)->tag('guides.directive')
        ->set(SidebarDirective::class)->tag('guides.directive')
        ->set(TipDirective::class)->tag('guides.directive')
        ->set(Title::class)->tag('guides.directive')
        ->set(Toctree::class)
            ->args([
                inline_service(ToctreeBuilder::class)->args([inline_service(GlobSearcher::class)->autowire(), service(UrlGenerator::class)])
            ])
            ->tag('guides.directive')
        ->set(TopicDirective::class)->tag('guides.directive')
        ->set(Uml::class)->tag('guides.directive')
        ->set(WarningDirective::class)->tag('guides.directive')
        ->set(Wrap::class)->tag('guides.directive')
        ->set(VersionAddedDirective::class)->tag('guides.directive')
        ->set(VersionChangedDirective::class)->tag('guides.directive')
        ->set(DeprecatedDirective::class)->tag('guides.directive')

        ->set(BestPracticeDirective::class)->tag('guides.directive')
        ->set(ScreencastDirective::class)->tag('guides.directive')
    ;
};
