# Composer Manifest

A Composer plugin which maintains a simple YAML file listing all installed
packages with their version numbers.

## Why?

When Composer performs an install or an update, the output tells you what has
been changed. But once that output is gone, it's hard to reconstitute it, as a
diff of the changes to the composer.lock file is hard to read.

This plugin writes a YAML file listing each package on one line, so a diff or
log can show you exactly what has been changed.

## Usage

Install the plugin.

```
composer require joachim-n/composer-manifest
```

Whenever packages are installed, updated, or removed, the plugin will update the
composer-manifest.yaml file in your project root. You should commit this to
version control at the same time as you commit changes to composer.json and
composer.lock to keep a history of changes.
