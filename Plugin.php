<?php

namespace ComposerManifest;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\Link;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
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
      $package_versions[$package->getName()] = $package->getPrettyVersion();
    }

    $yaml_data = [
      // 'content-hash' => $content_hash,
      'packages' => $package_versions,
    ];

    $yaml = Yaml::dump($yaml_data);
    file_put_contents('composer-manifest.yaml', $yaml);
  }

}
