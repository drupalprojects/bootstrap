<?php
/**
 * @file
 * Contains \Drupal\bootstrap\FormManager.
 */

namespace Drupal\bootstrap;

/**
 * Manages discovery and instantiation of Bootstrap form alters.
 */
class FormManager extends PluginManager {

  /**
   * Constructs a new \Drupal\bootstrap\FormManager object.
   *
   * @param \Drupal\bootstrap\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Form', 'Drupal\bootstrap\Form\FormInterface', 'Drupal\bootstrap\Annotation\BootstrapForm');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':form');
  }

}
