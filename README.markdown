# Symfony docs phpDocumentor Guides test

1. Installation:

   ```
   git clone --recurse-submodules https://github.com/wouterj/symfony-docs-guides
   cd symfony-docs-phpdoc
   composer update
   ```

2. Usage: `php ./vendor/bin/guides --theme rtd -vvv docs html`

3. View the rendered docs (if you have Caddy installed): `caddy start` and go to http://localhost:2000/
