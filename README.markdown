# Symfony docs phpDocumentor Guides test

```
git clone --recurse-submodules https://github.com/wouterj/symfony-docs-guides
cd symfony-docs-phpdoc
composer update
php lib/docs-builder/bin/docs-builder build:docs docs/ _build/output
```

TODO

* [ ] Directives are parsed as blockquotes or code blocks (maybe related to https://github.com/doctrine/rst-parser/pull/141 refactoring)
* [ ] Tons of "Invalid link" and "Invalid cross reference" errors
* [ ] Write a file collector that can exclude directories (when using with the real symfony-docs structure)
* [ ] Better support for theming? (see current TwigEnvironmentFactory)
