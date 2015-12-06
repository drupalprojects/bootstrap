<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\UpdateManager.
 */

namespace Drupal\bootstrap\Plugin;

use Drupal\bootstrap\Theme;

/**
 * Manages discovery and instantiation of Bootstrap updates.
 */
class UpdateManager extends PluginManager {

  /**
   * Constructs a new \Drupal\bootstrap\Plugin\UpdateManager object.
   *
   * @param \Drupal\bootstrap\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Plugin/Update', 'Drupal\bootstrap\Plugin\Update\UpdateInterface', 'Drupal\bootstrap\Annotation\BootstrapUpdate');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':update', $this->getCacheTags());
  }

}
