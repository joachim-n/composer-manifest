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
    example/beta-version: 1.0.0-beta2
    example/path-repository: dev-develop
    example/specific-commit: 'dev-main:394dd58814320e136b5b24e900ba3e0a428b73a8'
    example/stable-version: 2.8.12
    joachim-n/composer-manifest: 1.1.7
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
