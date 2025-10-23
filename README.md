# Composer Manifest

A Composer plugin which maintains a simple YAML file listing all installed
packages with their version numbers.

## Why?

When Composer performs an install or an update, the output tells you what has
been changed. But once that output is gone, it's hard to reconstitute it, as a
diff of the changes to the composer.lock file is hard to read.

This plugin writes a YAML file listing each package on one line, so a diff or
log can show you exactly what has been changed.

### Example:

```yaml
packages:
    composer/ca-bundle: 1.2.11
    composer/composer: 2.1.9
    composer/metadata-minifier: 1.0.0
    composer/semver: 3.2.5
    composer/spdx-licenses: 1.5.5
    composer/xdebug-handler: 2.0.2
    joachim-n/composer-manifest: 1.1.7
    justinrainbow/json-schema: 5.2.11
    psr/container: 1.1.1
    psr/log: 1.1.4
    react/promise: v2.8.0
    seld/jsonlint: 1.8.3
    seld/phar-utils: 1.1.2
    symfony/console: v5.3.7
    symfony/deprecation-contracts: v2.4.0
    symfony/filesystem: v5.3.4
    symfony/finder: v5.3.7
    symfony/polyfill-ctype: v1.23.0
    symfony/polyfill-intl-grapheme: v1.23.1
    symfony/polyfill-intl-normalizer: v1.23.0
    symfony/polyfill-mbstring: v1.23.1
    symfony/polyfill-php72: v1.20.0
    symfony/polyfill-php73: v1.23.0
    symfony/polyfill-php80: v1.23.1
    symfony/process: v5.3.7
    symfony/service-contracts: v2.4.0
    symfony/string: v5.3.7
    symfony/var-dumper: v4.4.15
    symfony/yaml: v4.4.15
```

## Usage

Install the plugin.

```
composer require joachim-n/composer-manifest
```

Whenever packages are installed, updated, or removed, the plugin will update the
composer-manifest.yaml file in your project root. You should commit this to
version control at the same time as you commit changes to composer.json and
composer.lock to keep a history of changes.

### Integrations

With [Renovate](https://github.com/renovatebot/renovate), use the fileFilters
config option to ensure the manifest file is committed by the renovate bot.
