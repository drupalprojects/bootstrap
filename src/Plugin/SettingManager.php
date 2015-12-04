<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\SettingManager.
 */

namespace Drupal\bootstrap\Plugin;

use Drupal\bootstrap\Theme;

/**
 * Manages discovery and instantiation of Bootstrap theme settings.
 */
class SettingManager extends PluginManager {

  /**
   * Constructs a new \Drupal\bootstrap\Plugin\SettingManager object.
   *
   * @param \Drupal\bootstrap\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Plugin/Setting', 'Drupal\bootstrap\Plugin\Setting\SettingInterface', 'Drupal\bootstrap\Annotation\BootstrapSetting');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':setting', ['theme_registry']);
  }

}
