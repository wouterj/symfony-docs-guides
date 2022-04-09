<?php

/*
 * This file is part of the Docs Builder package.
 *
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyDocsBuilder\DependencyInjection;

use Twig\Environment;
use Twig\Loader\LoaderInterface;

class TwigEnvironmentFactory
{
    public function __construct(
        private LoaderInterface $loader,
        private iterable $extensions,
    ) {}

    public function __invoke()
    {
        $twig = new Environment($this->loader);
        foreach ($this->extensions as $extension) {
            $twig->addExtension($extension);
        }

        return $twig;
    }
}
