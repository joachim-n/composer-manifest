<?php

namespace ComposerManifest;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Composer command to regenerate the manifest YAML file.
 */
class ManifestCommand extends BaseCommand {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('manifest:generate')
      ->setAliases(array('manifest', 'manifest:update'))
      ->setDescription('Regenerates the composer-manifest.yaml file.');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $composer = $this->getComposer();
    $plugin = new Plugin();
    $plugin->activate($composer, $this->getIO());
    $plugin->generateManifest();

    $this->getIO()->write('<info>composer-manifest.yaml has been regenerated.</info>');

    return 0;
  }

}
