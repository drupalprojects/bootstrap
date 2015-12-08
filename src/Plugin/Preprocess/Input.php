<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Input.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\bootstrap\Utility\Element;

/**
 * Pre-processes variables for the "input" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "input"
 * )
 */
class Input extends PluginBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    \Drupal\Core\Render\Element::setAttributes($variables['element'], ['id', 'name', 'value', 'type']);
    $element = new Element($variables['element']);
    $variables['icon'] = $element->getProperty('icon');
    $variables['attributes'] = $element->getAttributes();
    $variables['element'] = $element->getArray();
    $variables['label'] = $element->getProperty('value');
  }

}
