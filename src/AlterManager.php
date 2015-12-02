<?php
/**
 * @file
 * Contains \Drupal\bootstrap\AlterManager.
 */

namespace Drupal\bootstrap;

/**
 * Manages discovery and instantiation of Bootstrap hook alters.
 */
class AlterManager extends PluginManager {

  /**
   * Constructs a new \Drupal\bootstrap\AlterManager object.
   *
   * @param \Drupal\bootstrap\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Alter', 'Drupal\bootstrap\Alter\AlterInterface', 'Drupal\bootstrap\Annotation\BootstrapAlter');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':alter');
  }

}
