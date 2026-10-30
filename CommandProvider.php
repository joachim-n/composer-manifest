<?php

namespace ComposerManifest;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

/**
 * Commands Provider for Composer Manifest.
 */
class CommandProvider implements CommandProviderCapability {

  /**
   * {@inheritdoc}
   */
  public function getCommands() {
    return array(
      new ManifestCommand(),
    );
  }

}
