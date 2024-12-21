<?php

namespace ComposerManifest;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\AliasPackage;
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
    $packages = array_filter($localRepository->getPackages(), function ($package) {
      // Only include real packages, not their aliases. This is to prevent
      // inconsistent $packages order between install/require/update operations
      // from causing the manifest output to switch between the version string
      // of the original and the alias.
      return !($package instanceof AliasPackage);
    });

    // TODO: do we want to include the lock hash? Not sure it's useful, and it's
    // a PITA in merge conflicts.
    // $lock = $this->composer->getLocker()->getLockData();
    // $content_hash = $lock['content-hash'];

    foreach ($packages as $package) {
      $pretty_version = $package->getFullPrettyVersion(FALSE);
      // For backwards compatibility use ':' instead of space to separate
      // friendly name from revision hash.
      $output_version = str_replace(' ', ':', $pretty_version);
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
