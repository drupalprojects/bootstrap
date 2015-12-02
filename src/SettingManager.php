<?php
/**
 * @file
 * Contains \Drupal\bootstrap\SettingManager.
 */

namespace Drupal\bootstrap;

/**
 * Manages discovery and instantiation of Bootstrap theme settings.
 */
class SettingManager extends PluginManager {

  /**
   * Constructs a new \Drupal\bootstrap\SettingManager object.
   *
   * @param \Drupal\bootstrap\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Setting', NULL, 'Drupal\bootstrap\Annotation\BootstrapSetting');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':setting');
  }

}
