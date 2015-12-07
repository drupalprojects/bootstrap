<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\InputButton.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;
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
    $element = new Element($variables['element']);
    $element->addClass('btn');
    $element->colorize();
    $element->setIcon($element->getProperty('icon'));
    if ($size = Bootstrap::getTheme()->getSetting('button_size')) {
      $element->addClass($size);
    }
    parent::preprocess($variables, $hook, $info);
  }

}
