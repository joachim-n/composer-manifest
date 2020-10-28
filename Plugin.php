<?php

namespace ComposerManifest;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use Composer\EventDispatcher\Event as BaseEvent;
use Symfony\Component\Yaml\Yaml;

/**
 * Composer manifest plugin.
 */
class Plugin implements PluginInterface, EventSubscriberInterface {

  /**
   * @var \Composer\Composer $composer
   */
  protected $composer;

  /**
   * @param \Composer\Composer $composer
   * @param \Composer\IO\IOInterface $io
   */
  public function activate(Composer $composer, IOInterface $io) {
    // Development: this makes symfony var-dumper work.
    // See https://github.com/composer/composer/issues/7911
    // include './vendor/symfony/var-dumper/Resources/functions/dump.php';

    $this->composer = $composer;
  }

  /**
   * Returns an array of event names this subscriber wants to listen to.
   */
  public static function getSubscribedEvents() {
    return array(
      ScriptEvents::POST_INSTALL_CMD => array('updateManifest'),
      ScriptEvents::POST_UPDATE_CMD => array('updateManifest'),
      PackageEvents::POST_PACKAGE_UNINSTALL => array('updateManifest', 10),
    );
  }

  /**
   * Rewrites the manifest YAML file.
   *
   * @param \Composer\EventDispatcher\Event $event
   */
  public function updateManifest(BaseEvent $event) {
    $repositoryManager = $this->composer->getRepositoryManager();
    $localRepository = $repositoryManager->getLocalRepository();
    $packages = $localRepository->getPackages();

    // TODO: do we want to include the lock hash? Not sure it's useful, and it's
    // a PITA in merge conflicts.
    // $lock = $this->composer->getLocker()->getLockData();
    // $content_hash = $lock['content-hash'];

    $package_names = [];
    foreach ($packages as $package) {
      $pretty_version = $package->getPrettyVersion();
      $output_version = $pretty_version;

      // If the package is using a dev version, append the git commit SHA so
      // that we can see changes to the actual version used.
      // (No idea why it's sometimes a dev- prefix and sometimes a -dev suffix,
      // but check for both.)
      if (substr($pretty_version, 0, 4) == 'dev-' || substr($pretty_version, -4) == '-dev') {
        $reference = $package->getSourceReference();

        // Fall back to the dist reference if the source reference is empty.
        if (empty($reference)) {
          $reference = $package->getDistReference();
        }

        $output_version .= ':' . $reference;
      }

      $package_versions[$package->getName()] = $output_version;
    }

    // Make sure the packages are sorted consistently. We need this because in
    // some cases, new packages are at the end of the list returned by
    // getPackages() rather than in their correct place in the alphabetical
    // order: WTF.
    ksort($package_versions);

    $yaml_data = [
      // 'content-hash' => $content_hash,
      'packages' => $package_versions,
    ];

    $yaml = Yaml::dump($yaml_data);
    file_put_contents('composer-manifest.yaml', $yaml);
  }

  /**
   * {@inheritdoc}
   */
  public function deactivate(Composer $composer, IOInterface $io) {
    // Do nothing.
  }

  /**
   * {@inheritdoc}
   */
  public function uninstall(Composer $composer, IOInterface $io) {
    if (file_exists('composer-manifest.yaml')) {
      unlink('composer-manifest.yaml');
    }
  }
}
