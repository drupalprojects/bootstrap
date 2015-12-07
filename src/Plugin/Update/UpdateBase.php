<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Update\UpdateBase.
 */

namespace Drupal\bootstrap\Plugin\Update;

use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Theme;
use Drupal\Core\Plugin\PluginBase;

/**
 * Base class for an update.
 */
class UpdateBase extends PluginBase implements UpdateInterface {

  /**
   * The currently set theme object.
   *
   * @var \Drupal\bootstrap\Theme
   */
  protected $theme;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    if (!isset($configuration['theme'])) {
      $configuration['theme'] = Bootstrap::getTheme();
    }
    $this->theme = $configuration['theme'];
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return isset($this->pluginDefinition['description']) ? $this->pluginDefinition['description'] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getLevel() {
    return isset($this->pluginDefinition['level']) ? $this->pluginDefinition['level'] : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return !empty($this->pluginDefinition['title']) ? $this->pluginDefinition['title'] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function update(Theme $theme) {}

}
