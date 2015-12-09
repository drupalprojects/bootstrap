<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\InputButton.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Utility\Element;

/**
 * Pre-processes variables for the "input__button" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "input__button"
 * )
 */
class InputButton extends Input implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $element = Element::create($variables['element']);
    $element->colorize();
    $element->setIcon($element->getProperty('icon'));
    $variables['icon_only'] = $element->getProperty('icon_only');
    $variables['label'] = $element->getProperty('value');
    parent::preprocess($variables, $hook, $info);
  }

}
