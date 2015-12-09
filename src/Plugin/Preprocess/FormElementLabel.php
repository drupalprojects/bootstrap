<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\FormElementLabel.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\bootstrap\Utility\Element;
use Drupal\Component\Utility\NestedArray;

/**
 * Pre-processes variables for the "form_element_label" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "form_element_label"
 * )
 */
class FormElementLabel extends PluginBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $element = new Element($variables['element']);
    $variables['attributes'] = NestedArray::mergeDeepArray([$variables['attributes'], $element->getAttributes()], TRUE);
  }

}
