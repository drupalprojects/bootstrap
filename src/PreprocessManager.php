<?php
/**
 * @file
 * Contains \Drupal\bootstrap\PreprocessManager.
 */

namespace Drupal\bootstrap;

/**
 * Manages discovery and instantiation of Bootstrap preprocess hooks.
 */
class PreprocessManager extends PluginManager {

  /**
   * Constructs a new \Drupal\bootstrap\PreprocessManager object.
   *
   * @param \Drupal\bootstrap\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Preprocess', 'Drupal\bootstrap\Preprocess\PreprocessInterface', 'Drupal\bootstrap\Annotation\BootstrapPreprocess');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':preprocess');
  }

}
