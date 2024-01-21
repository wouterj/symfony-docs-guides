Read The Docs Guides Theme
==========================

This package integrates the popular [Read The Docs Sphinx theme](https://sphinx-rtd-theme.readthedocs.io)
in the [phpDocumentor Guides](https://github.com/phpdocumentor/guides)
library.

Installation
------------

 1. Install the package using Composer: `composer require wouterj/guides-readthedocs`
 2. Update the `guides.xml` file to enable the extension:

    ```xml
    <!-- guides.xml -->
    <?xml version="1.0" encoding="UTF-8" ?>
    <guides xmlns="https://www.phpdoc.org/guides"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="https://www.phpdoc.org/guides packages/guides-cli/resources/schema/guides.xsd">

        <extension class="WouterJ\ReadTheDocs"/>
    </guides>
    ```
 3. Render the documentation using the new theme: `./vendor/bin/guides --theme rtd` 
