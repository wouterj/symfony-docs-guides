<?php

/*
 * This file is part of the Docs Builder package.
 *
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyDocsBuilder\Twig;

use SymfonyDocsBuilder\Build\BuildConfig;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class EnvironmentFactory
{
    public function __construct(
        private BuildConfig $buildConfig,
        private FilesystemLoader $loader,
        private iterable $extensions,
    ) {}

    public function __invoke(): Environment
    {
        if ($theme = $this->buildConfig->getTheme()) {
            $themeDir = sprintf('%s/templates/%s/%s/guides', dirname(__DIR__, 2), $theme, $this->buildConfig->getFormat());
            if (file_exists($themeDir)) {
                $this->loader->prependPath($themeDir);
            }
        }

        $twig = new Environment($this->loader);
        foreach ($this->extensions as $extension) {
            $twig->addExtension($extension);
        }

        return $twig;
    }
}
